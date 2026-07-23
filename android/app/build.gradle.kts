plugins {
    id("com.android.application")
    // --- PUSH NOTIFICATIONS (PHPHONE) ---
    // Para activar Firebase, descomenta la siguiente línea:
    // id("com.google.gms.google-services")
}

android {
    namespace = "com.example.phphone"
    compileSdk = 36

    defaultConfig {
        applicationId = "com.example.phphone"
        minSdk = 24
        targetSdk = 36
        versionCode = 1
        versionName = "1.0"
        

        
        ndk {
            abiFilters.add("arm64-v8a")
        }
    }

    androidResources {
        // Se sobrescribe el patrón por defecto para evitar que excluya archivos ocultos como .env
        ignoreAssetsPattern = "!.svn:!.git:!.ds_store:!*.scc:!CVS:!thumbs.db:!picasa.ini:!*~"
    }

    signingConfigs {
        create("release") {
            storeFile = file("release.jks")
            storePassword = "phphone123"
            keyAlias = "kie-release-key"
            keyPassword = "phphone123"
        }
    }

    buildTypes {
                release {
            signingConfig = signingConfigs.getByName("release")
            isMinifyEnabled = false
            proguardFiles(getDefaultProguardFile("proguard-android-optimize.txt"), "proguard-rules.pro")
        }
    }



    compileOptions {
        sourceCompatibility = JavaVersion.VERSION_17
        targetCompatibility = JavaVersion.VERSION_17
    }


}

dependencies {
    // Phphone Engine Pure Native - Zero AndroidX Dependencies Bloat!
    implementation("org.nanohttpd:nanohttpd:2.3.1")
    
    // --- PUSH NOTIFICATIONS (PHPHONE) ---
    // Para activar Firebase, descomenta las siguientes líneas:
    // implementation(platform("com.google.firebase:firebase-bom:33.1.2"))
    // implementation("com.google.firebase:firebase-messaging")

    // --- IN APP PURCHASES (PHPHONE) ---
    // Para activar Compras Integradas, descomenta la siguiente línea:
    // implementation("com.android.billingclient:billing:6.2.1")
}

val phphoneSourceDir = if (project.hasProperty("phphoneSourceDir")) {
    project.property("phphoneSourceDir") as String
} else {
    "../../src"
}

tasks.register<Sync>("syncPhpAssets") {
    from(phphoneSourceDir)
    into("src/main/assets/src")
}

tasks.named("preBuild") {
    dependsOn("syncPhpAssets")
}
