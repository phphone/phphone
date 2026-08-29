#!/bin/bash
set -e

# ==============================================================================
# Manual iOS Simulator Compiler Script (Incremental & .nosync Optimized)
# ==============================================================================

SDK_PATH=$(xcrun --sdk iphonesimulator --show-sdk-path)
ARCH="arm64"
TARGET="arm64-apple-ios12.0-simulator"
BUILD_DIR="build_manual.nosync"
PHP_BUILD_IOS="$(cd "$(dirname "$0")" && pwd)/build_ios"

# Clean only if --clean flag is passed
if [ "$1" == "--clean" ]; then
    echo "🧹 Cleaning previous manual build..."
    rm -rf "$BUILD_DIR"
fi

mkdir -p "$BUILD_DIR"

echo "📦 Compiling GCDWebServer..."
GCD_FILES=(
    "App/GCDWebServer/Core/GCDWebServer.m"
    "App/GCDWebServer/Core/GCDWebServerConnection.m"
    "App/GCDWebServer/Core/GCDWebServerFunctions.m"
    "App/GCDWebServer/Core/GCDWebServerRequest.m"
    "App/GCDWebServer/Core/GCDWebServerResponse.m"
    "App/GCDWebServer/Responses/GCDWebServerDataResponse.m"
    "App/GCDWebServer/Responses/GCDWebServerErrorResponse.m"
    "App/GCDWebServer/Responses/GCDWebServerFileResponse.m"
    "App/GCDWebServer/Responses/GCDWebServerStreamedResponse.m"
    "App/GCDWebServer/Requests/GCDWebServerDataRequest.m"
    "App/GCDWebServer/Requests/GCDWebServerMultiPartFormRequest.m"
    "App/GCDWebServer/Requests/GCDWebServerFileRequest.m"
    "App/GCDWebServer/Requests/GCDWebServerURLEncodedFormRequest.m"
)

for file in "${GCD_FILES[@]}"; do
    obj_file="$BUILD_DIR/$(basename "${file%.m}").o"
    if [ ! -f "$obj_file" ] || [ "$file" -nt "$obj_file" ]; then
        echo "  -> Compiling $file"
        clang -c -target "$TARGET" -isysroot "$SDK_PATH" -fobjc-arc \
            -I App \
            -I App/GCDWebServer/Core \
            -I App/GCDWebServer/Responses \
            -I App/GCDWebServer/Requests \
            "$file" -o "$obj_file"
    else
        # Reuse cache silently
        true
    fi
done
echo "  -> GCDWebServer compilation check complete."

echo "⚙️ Compiling kie_engine.mm..."
engine_obj="$BUILD_DIR/kie_engine.o"
if [ ! -f "$engine_obj" ] || [ "App/kie_engine.mm" -nt "$engine_obj" ]; then
    echo "  -> Compiling App/kie_engine.mm"
    clang++ -c -target "$TARGET" -isysroot "$SDK_PATH" -fobjc-arc -std=c++14 \
        -I App \
        -I App/GCDWebServer/Core \
        -I "$PHP_BUILD_IOS/iphonesimulator-arm64/include/php" \
        -I "$PHP_BUILD_IOS/iphonesimulator-arm64/include/php/main" \
        -I "$PHP_BUILD_IOS/iphonesimulator-arm64/include/php/Zend" \
        -I "$PHP_BUILD_IOS/iphonesimulator-arm64/include/php/TSRM" \
        App/kie_engine.mm -o "$engine_obj"
else
    echo "  -> Reusing cached kie_engine.o"
fi

echo "⚡ Compiling Swift files..."
app_delegate_obj="$BUILD_DIR/AppDelegate.o"
view_controller_obj="$BUILD_DIR/ViewController.o"

if [ ! -f "$app_delegate_obj" ] || [ ! -f "$view_controller_obj" ] || \
   [ "App/AppDelegate.swift" -nt "$app_delegate_obj" ] || \
   [ "App/ViewController.swift" -nt "$view_controller_obj" ]; then
    echo "  -> Compiling App/AppDelegate.swift & ViewController.swift"
    swiftc -c -target "$TARGET" -sdk "$SDK_PATH" \
        -import-objc-header App/Bridging-Header.h \
        -I App \
        -I App/GCDWebServer/Core \
        -I App/GCDWebServer/Responses \
        -I App/GCDWebServer/Requests \
        -I . \
        App/AppDelegate.swift App/ViewController.swift
    mv AppDelegate.o "$BUILD_DIR/"
    mv ViewController.o "$BUILD_DIR/"
else
    echo "  -> Reusing cached Swift objects"
fi

echo "🔗 Linking executable..."
OBJS=($BUILD_DIR/*.o)

LIBPHP="$PHP_BUILD_IOS/universal/libphp.xcframework/ios-arm64_x86_64-simulator/libphp.a"
LIBSSL="$PHP_BUILD_IOS/openssl-iphonesimulator-arm64/lib/libssl.a"
LIBCRYPTO="$PHP_BUILD_IOS/openssl-iphonesimulator-arm64/lib/libcrypto.a"
LIBCURL="$PHP_BUILD_IOS/curl-iphonesimulator-arm64/lib/libcurl.a"

swiftc -target "$TARGET" -sdk "$SDK_PATH" \
    -Xlinker -objc_abi_version -Xlinker 2 \
    "${OBJS[@]}" "$LIBPHP" "$LIBSSL" "$LIBCRYPTO" "$LIBCURL" \
    -lc++ \
    -lresolv -lsqlite3 -lz \
    -framework Foundation -framework UIKit -framework WebKit -framework CoreLocation -framework CoreMotion -framework UserNotifications -framework MobileCoreServices -framework Contacts \
    -o "$BUILD_DIR/Phphone"

# Read metadata if available
META_FILE="phphone_meta.json"
APP_NAME="Phphone"
BUNDLE_ID="com.phphone.Phphone"

if [ -f "$META_FILE" ]; then
    PARSED_NAME=$(grep -o '"app_name": *"[^"]*"' "$META_FILE" | head -1 | sed 's/"app_name": *"//;s/"//')
    PARSED_PKG=$(grep -o '"package_name": *"[^"]*"' "$META_FILE" | head -1 | sed 's/"package_name": *"//;s/"//')
    if [ -n "$PARSED_NAME" ]; then APP_NAME="$PARSED_NAME"; fi
    if [ -n "$PARSED_PKG" ]; then BUNDLE_ID="$PARSED_PKG"; fi
fi

echo "📱 Creating app bundle ($APP_NAME - $BUNDLE_ID)..."
APP_BUNDLE="$BUILD_DIR/$APP_NAME.app"
rm -rf "$BUILD_DIR/"*.app
mkdir -p "$APP_BUNDLE"

# Copy executable
cp "$BUILD_DIR/Phphone" "$APP_BUNDLE/$APP_NAME"

# Copy and process Info.plist
sed -e "s/\$(EXECUTABLE_NAME)/$APP_NAME/g" \
    -e 's/\$(DEVELOPMENT_LANGUAGE)/en/g' \
    -e 's/\$(PRODUCT_BUNDLE_PACKAGE_TYPE)/APPL/g' \
    -e "s/\$(PRODUCT_NAME)/$APP_NAME/g" \
    -e "s/\$(PRODUCT_BUNDLE_IDENTIFIER)/$BUNDLE_ID/g" \
    App/Info.plist > "$APP_BUNDLE/Info.plist"

# Copy PkgInfo
echo -n "APPL????" > "$APP_BUNDLE/PkgInfo"

# Compile and copy App Icons & Splash assets via actool if available
if [ -d "App/Assets.xcassets" ]; then
    echo "🎨 Compiling App Icons & Assets..."
    xcrun actool "App/Assets.xcassets" \
        --compile "$APP_BUNDLE" \
        --platform iphonesimulator \
        --target-device iphone \
        --target-device ipad \
        --minimum-deployment-target 12.0 \
        --app-icon AppIcon \
        --output-format human-readable-text \
        --output-partial-info-plist "$BUILD_DIR/partial-info.plist" || true
fi

# Copy resources/assets (src directory)
echo "📁 Copying assets..."
mkdir -p "$APP_BUNDLE/src"
SRC_DIR=${PHPHONE_SRC_DIR:-../src/}
cp -r "$SRC_DIR" "$APP_BUNDLE/src/"

echo "✅ App bundle created at $APP_BUNDLE!"
