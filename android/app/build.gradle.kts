plugins {
    id("com.android.application")
}

if (file("google-services.json").exists()) {
    apply(plugin = "com.google.gms.google-services")
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
    implementation(platform("com.google.firebase:firebase-bom:33.1.2"))
    implementation("com.google.firebase:firebase-messaging")

    // --- IN APP PURCHASES (PHPHONE) ---
    // Para activar Compras Integradas, descomenta la siguiente línea:
    // implementation("com.android.billingclient:billing:6.2.1")
}

val phphoneSourceDir = if (project.hasProperty("phphoneSourceDir")) {
    project.property("phphoneSourceDir") as String
} else {
    "../../src"
}

// Cargar reglas dinámicas de .phphoneignore si existe
val ignorePatterns = mutableListOf<String>()
// Exclusiones universales estándar
ignorePatterns.addAll(listOf(
    "**/node_modules/**",
    "**/.git/**",
    "**/.svn/**",
    "**/.idea/**",
    "**/.vscode/**",
    "**/.DS_Store",
    "**/Thumbs.db",
    "**/*.log"
))

val ignoreFiles = listOf(
    file("../../.phphoneignore"),
    file("../../src/.phphoneignore")
)

for (ignoreFile in ignoreFiles) {
    if (ignoreFile.exists()) {
        ignoreFile.readLines().forEach { rawLine ->
            val line = rawLine.trim()
            if (line.isNotEmpty() && !line.startsWith("#")) {
                val clean = line.removePrefix("./").removePrefix("/")
                if (clean.endsWith("/")) {
                    val dirName = clean.removeSuffix("/")
                    ignorePatterns.add("**/$dirName/**")
                } else if (clean.contains("*")) {
                    ignorePatterns.add("**/$clean")
                } else {
                    ignorePatterns.add("**/$clean")
                    ignorePatterns.add("**/$clean/**")
                }
            }
        }
    }
}

tasks.register<Sync>("syncPhpAssets") {
    from(phphoneSourceDir) {
        ignorePatterns.distinct().forEach { pattern ->
            exclude(pattern)
        }
    }
    into("src/main/assets/src")
}

tasks.named("preBuild") {
    dependsOn("syncPhpAssets")
}
