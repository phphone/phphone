// Top-level build file where you can add configuration options common to all sub-projects/modules.
plugins {
  alias(libs.plugins.android.application) apply false
  alias(libs.plugins.compose.compiler) apply false
  alias(libs.plugins.kotlin.serialization) apply false
  
  // --- PUSH NOTIFICATIONS (PHPHONE) ---
  // Para activar Firebase, descomenta la siguiente línea:
  // id("com.google.gms.google-services") version "4.4.1" apply false
}