package com.example.phphone

import android.annotation.SuppressLint
import android.app.Activity
import android.os.Bundle
import android.webkit.WebResourceRequest
import android.webkit.WebResourceResponse
import android.webkit.WebView
import android.webkit.WebViewClient

import android.content.BroadcastReceiver
import android.content.Context
import android.content.Intent
import android.content.IntentFilter
import android.os.Build
import android.view.View
import android.media.MediaPlayer
import android.net.Uri

import java.io.File
import java.io.FileInputStream
import java.io.FileOutputStream

class MainActivity : Activity() {

    // Declaración nativa de la función JNI implementada en C++
    external fun processPhpRequest(url: String, method: String, appPath: String): String

    private lateinit var reloadReceiver: BroadcastReceiver
    private lateinit var webView: WebView
    private var localWebServer: KieWebServer? = null

    // Sincronización de hardware (CountDownLatch)
    var gpsLatch: java.util.concurrent.CountDownLatch? = null
    var gpsResult: String? = null
    
    var cameraLatch: java.util.concurrent.CountDownLatch? = null
    var cameraResult: String? = null
    
    var notificationLatch: java.util.concurrent.CountDownLatch? = null
    var notificationResult: Boolean = false

    var biometricLatch: java.util.concurrent.CountDownLatch? = null
    var biometricResult: Boolean = false

    var galleryLatch: java.util.concurrent.CountDownLatch? = null
    var galleryResult: String? = null

    var micLatch: java.util.concurrent.CountDownLatch? = null
    var micResult: Boolean = false

    var filePickerLatch: java.util.concurrent.CountDownLatch? = null
    var filePickerResult: String? = null

    var gyroscopeLatch: java.util.concurrent.CountDownLatch? = null
    var gyroscopeResult: String? = null
    
    // Variables para Gyroscope Streaming
    @Volatile var currentGyroX: Float = 0f
    @Volatile var currentGyroY: Float = 0f
    @Volatile var currentGyroZ: Float = 0f
    var gyroSensorListener: android.hardware.SensorEventListener? = null
    var isGyroRunning = false
    
    // --- PUSH NOTIFICATIONS (PHPHONE) ---
    // var pushTokenLatch: java.util.concurrent.CountDownLatch? = null
    // var pushTokenResult: String? = null

    // --- IN APP PURCHASES (PHPHONE) ---
    // var billingClient: com.android.billingclient.api.BillingClient? = null
    // var purchaseLatch: java.util.concurrent.CountDownLatch? = null
    // var purchaseResult: String? = null

    var contactsLatch: java.util.concurrent.CountDownLatch? = null
    var contactsResult: String? = null

    private var mediaPlayer: MediaPlayer? = null
    private var audioRecorder: android.media.MediaRecorder? = null
    private var audioRecordPath: String = ""

    companion object {
        var KIE_ZOOM_ENABLED = false // CONFIG_ZOOM

        init {
            // Modo Titán: Cargar criptografía antes que el motor
            System.loadLibrary("crypto")
            System.loadLibrary("ssl")
            
            System.loadLibrary("php")
            System.loadLibrary("kie_engine")
        }
    }

    @SuppressLint("SetJavaScriptEnabled")
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        
        // --- EDGE-TO-EDGE ---
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.R) {
            window.setDecorFitsSystemWindows(false)
        } else {
            @Suppress("DEPRECATION")
            window.decorView.systemUiVisibility = (
                View.SYSTEM_UI_FLAG_LAYOUT_STABLE
                or View.SYSTEM_UI_FLAG_LAYOUT_HIDE_NAVIGATION
                or View.SYSTEM_UI_FLAG_LAYOUT_FULLSCREEN
            )
        }
        window.statusBarColor = android.graphics.Color.TRANSPARENT
        window.navigationBarColor = android.graphics.Color.TRANSPARENT

        // Extraer archivos PHP del APK al disco interno del teléfono (Sobrescribir en desarrollo)
        val kieAppPath = File(filesDir, "kie_app")
        val srcPath = File(kieAppPath, "src")
        if (srcPath.exists()) {
            srcPath.deleteRecursively()
        }
        
        kieAppPath.mkdirs()
        copyAssetFolder("src", kieAppPath.absolutePath + "/src")
        android.util.Log.i("Phphone", "Archivos PHP nativos extraídos en: ${kieAppPath.absolutePath}")

        // Iniciar el servidor local del puente de hardware
        try {
            localWebServer = KieWebServer(this, 8081)
            localWebServer?.start()
            android.util.Log.i("Phphone", "Super Controlador iniciado en 127.0.0.1:8081")
        } catch (e: Exception) {
            android.util.Log.e("Phphone", "Error al iniciar KieWebServer: ${e.message}")
        }

        webView = WebView(this).apply {
            settings.javaScriptEnabled = true
            settings.domStorageEnabled = true
            settings.allowFileAccess = true
            settings.allowContentAccess = true
            // Desactivar caché estricto para ver los cambios del CSS/HTML instantáneamente
            settings.cacheMode = android.webkit.WebSettings.LOAD_NO_CACHE
            clearCache(true)
            if (!KIE_ZOOM_ENABLED) {
                settings.setSupportZoom(false)
                settings.builtInZoomControls = false
                settings.displayZoomControls = false
                overScrollMode = WebView.OVER_SCROLL_NEVER
            } else {
                settings.setSupportZoom(true)
                settings.builtInZoomControls = true
                settings.displayZoomControls = false
                overScrollMode = WebView.OVER_SCROLL_ALWAYS
            }

            webChromeClient = object : android.webkit.WebChromeClient() {
                override fun onConsoleMessage(consoleMessage: android.webkit.ConsoleMessage?): Boolean {
                    android.util.Log.e("WebConsole", "${consoleMessage?.message()} -- From line ${consoleMessage?.lineNumber()}")
                    return super.onConsoleMessage(consoleMessage)
                }

                // JavaScript: alert("mensaje")
                override fun onJsAlert(view: android.webkit.WebView?, url: String?, message: String?, result: android.webkit.JsResult?): Boolean {
                    android.app.AlertDialog.Builder(this@MainActivity)
                        .setMessage(message)
                        .setPositiveButton("OK") { _, _ -> result?.confirm() }
                        .setOnCancelListener { result?.cancel() }
                        .show()
                    return true
                }

                // JavaScript: confirm("¿Estás seguro?")
                override fun onJsConfirm(view: android.webkit.WebView?, url: String?, message: String?, result: android.webkit.JsResult?): Boolean {
                    android.app.AlertDialog.Builder(this@MainActivity)
                        .setMessage(message)
                        .setPositiveButton("OK") { _, _ -> result?.confirm() }
                        .setNegativeButton("Cancelar") { _, _ -> result?.cancel() }
                        .setOnCancelListener { result?.cancel() }
                        .show()
                    return true
                }

                // JavaScript: prompt("Escribe algo:", "valor por defecto")
                override fun onJsPrompt(view: android.webkit.WebView?, url: String?, message: String?, defaultValue: String?, result: android.webkit.JsPromptResult?): Boolean {
                    val input = android.widget.EditText(this@MainActivity)
                    input.setText(defaultValue)
                    android.app.AlertDialog.Builder(this@MainActivity)
                        .setMessage(message)
                        .setView(input)
                        .setPositiveButton("OK") { _, _ -> result?.confirm(input.text.toString()) }
                        .setNegativeButton("Cancelar") { _, _ -> result?.cancel() }
                        .setOnCancelListener { result?.cancel() }
                        .show()
                    return true
                }
            }

            
            webViewClient = object : WebViewClient() {
                override fun shouldOverrideUrlLoading(view: WebView?, request: WebResourceRequest?): Boolean {
                    val url = request?.url.toString()
                    
                    // 1. Es nuestra app: Dejamos que el WebView lo cargue internamente
                    if (url.startsWith("http://kie.local") || url.startsWith("file://")) {
                        return false // false = WebView, encárgate tú
                    }
                    
                    // 2. Es un enlace externo o protocolo especial: Abrir en app del sistema
                    try {
                        val intent = Intent(Intent.ACTION_VIEW, Uri.parse(url))
                        this@MainActivity.startActivity(intent)
                        return true // true = Ya lo manejé, WebView ignóralo
                    } catch (e: Exception) {
                        return false
                    }
                }

                override fun shouldInterceptRequest(
                    view: WebView?,
                    request: WebResourceRequest?
                ): WebResourceResponse? {
                    val url = request?.url.toString()
                    val method = request?.method ?: "GET"
                    
                    // Solo interceptamos nuestro dominio local
                    if (url.startsWith("http://kie.local")) {
                        val path = java.net.URL(url).path

                        // 1. Peticiones a PHP o rutas dinámicas van directo al cerebro en C++
                        if (!path.contains(".") || path.endsWith(".php")) {
                            val responseStr = processPhpRequest(url, method, kieAppPath.absolutePath)
                            val inputStream = java.io.ByteArrayInputStream(responseStr.toByteArray(Charsets.UTF_8))
                            // Si es API asume JSON, si es ruta normal asume HTML
                            val mime = if (path.startsWith("/api/")) "application/json" else "text/html"
                            return WebResourceResponse(mime, "UTF-8", inputStream)
                        } 
                        // 2. Archivos estáticos puros (JS, CSS, IMG) se leen del disco duro
                        else {
                            var assetPath = "src" + path
                            
                            try {
                                val targetFile = File(kieAppPath, assetPath)
                                if (!targetFile.exists()) {
                                    throw Exception("File not found on disk")
                                }
                                val mimeType = when {
                                    assetPath.endsWith(".html") -> "text/html"
                                    assetPath.endsWith(".css") -> "text/css"
                                    assetPath.endsWith(".js") -> "application/javascript"
                                    assetPath.endsWith(".png") -> "image/png"
                                    assetPath.endsWith(".jpg") || assetPath.endsWith(".jpeg") -> "image/jpeg"
                                    else -> "text/plain"
                                }
                                
                                if (KieSecrets.IS_ENCRYPTED) {
                                    val bytes = targetFile.readBytes()
                                    val magic = "KIE_ENC:".toByteArray(Charsets.UTF_8)
                                    if (bytes.size >= 24 && bytes.copyOfRange(0, 8).contentEquals(magic)) {
                                        try {
                                            val iv = bytes.copyOfRange(8, 24)
                                            val encrypted = bytes.copyOfRange(24, bytes.size)
                                            
                                            val keyBytes = KieSecrets.AES_KEY_HEX.chunked(2).map { it.toInt(16).toByte() }.toByteArray()
                                            val secretKeySpec = javax.crypto.spec.SecretKeySpec(keyBytes, "AES")
                                            val ivParameterSpec = javax.crypto.spec.IvParameterSpec(iv)
                                            
                                            val cipher = javax.crypto.Cipher.getInstance("AES/CBC/PKCS5Padding")
                                            cipher.init(javax.crypto.Cipher.DECRYPT_MODE, secretKeySpec, ivParameterSpec)
                                            
                                            val decryptedBytes = cipher.doFinal(encrypted)
                                            val inputStream = java.io.ByteArrayInputStream(decryptedBytes)
                                            return WebResourceResponse(mimeType, "UTF-8", inputStream)
                                        } catch (e: Exception) {
                                            // Fallback silente en caso de archivo no encriptado (ej: inyectado dinámicamente)
                                        }
                                    }
                                }

                                val inputStream = FileInputStream(targetFile)
                                return WebResourceResponse(mimeType, "UTF-8", inputStream)
                            } catch (e: Exception) {
                                android.util.Log.e("Phphone", "Asset no encontrado o error: $assetPath")
                            }
                        }
                    }
                    
                    return super.shouldInterceptRequest(view, request)
                }
            }
        }

        setContentView(webView)
        
        // Registrar el BroadcastReceiver para el Hot Reloading
        reloadReceiver = object : BroadcastReceiver() {
            private var isReloading = false
            
            override fun onReceive(context: Context?, intent: Intent?) {
                if (intent?.action == "com.example.phphone.RELOAD") {
                    if (isReloading) {
                        android.util.Log.w("Phphone", "⏳ Ignorando Hot Reload: Ya hay una recarga en curso (Previniendo Bailout)")
                        return
                    }
                    
                    isReloading = true
                    android.util.Log.i("Phphone", "🔥 Hot Reload Triggered!")
                    
                    // Limpiar la caché explícitamente antes de recargar
                    webView.clearCache(true)
                    webView.reload()
                    
                    // Liberar el candado después de un tiempo prudencial o cuando la página termine de cargar
                    webView.postDelayed({
                        isReloading = false
                        android.util.Log.i("Phphone", "✅ Hot Reload completado, candado liberado")
                    }, 1000) // 1 segundo de candado mínimo por recarga
                }
            }
        }
        
        val filter = IntentFilter("com.example.phphone.RELOAD")
        if (android.os.Build.VERSION.SDK_INT >= android.os.Build.VERSION_CODES.TIRAMISU) {
            registerReceiver(reloadReceiver, filter, Context.RECEIVER_EXPORTED)
        } else {
            @Suppress("UnspecifiedRegisterReceiverFlag")
            registerReceiver(reloadReceiver, filter)
        }

        // Disparamos la petición HTTP inicial (Auto-detección SPA Web o PHP nativo)
        if (File(kieAppPath, "src/index.html").exists()) {
            android.util.Log.i("Phphone", "index.html detectado, arrancando en modo SPA Web")
            webView.loadUrl("http://kie.local/index.html")
        } else {
            android.util.Log.i("Phphone", "Arrancando motor PHP por defecto")
            webView.loadUrl("http://kie.local/")
        }
    }

    
    override fun onPause() {
        super.onPause()
        // PHPHONE_INJECT:BACKGROUND_AUDIO_PAUSE
    }

    override fun onResume() {
        super.onResume()
        // PHPHONE_INJECT:BACKGROUND_AUDIO_RESUME
    }

    override fun onDestroy() {
        super.onDestroy()
        unregisterReceiver(reloadReceiver)
        stopNativeAudio()
        stopAudioRecording()
    }

    // --- NUEVOS MÉTODOS PARA AUDIO NATIVO ---
    fun playNativeAudio(path: String, loop: Boolean) {
        stopNativeAudio() // Detener audio anterior si lo hay

        try {
            mediaPlayer = MediaPlayer()
            
            if (path.startsWith("http://") || path.startsWith("https://")) {
                mediaPlayer?.setDataSource(path)
            } else {
                // Asumimos que la ruta es relativa a los assets
                // En Phphone, los assets se copiaron a filesDir/kie_app/src
                val file = File(filesDir, "kie_app/src/$path")
                if (file.exists()) {
                    mediaPlayer?.setDataSource(file.absolutePath)
                } else {
                    android.util.Log.e("Phphone", "Archivo de audio no encontrado: ${file.absolutePath}")
                    return
                }
            }

            mediaPlayer?.isLooping = loop
            mediaPlayer?.prepare()
            mediaPlayer?.start()
        } catch (e: Exception) {
            android.util.Log.e("Phphone", "Error al reproducir audio nativo: ${e.message}")
        }
    }

    fun stopNativeAudio() {
        try {
            mediaPlayer?.let {
                if (it.isPlaying) {
                    it.stop()
                }
                it.release()
            }
            mediaPlayer = null
        } catch (e: Exception) {
            android.util.Log.e("Phphone", "Error al detener audio nativo: ${e.message}")
        }
    }

    private fun copyAssetFolder(assetPath: String, targetPath: String) {
        val assetsList = assets.list(assetPath)
        
        if (assetsList.isNullOrEmpty()) {
            // Es un archivo o una carpeta vacía
            copyAssetFile(assetPath, targetPath)
        } else {
            // Es una carpeta con contenido
            val targetFolder = File(targetPath)
            if (!targetFolder.exists()) targetFolder.mkdirs()
            
            for (asset in assetsList) {
                copyAssetFolder("$assetPath/$asset", "$targetPath/$asset")
            }
        }
    }

    private fun copyAssetFile(assetFilePath: String, targetFilePath: String) {
        try {
            val inputStream = assets.open(assetFilePath)
            val outFile = File(targetFilePath)
            outFile.parentFile?.mkdirs()
            val outputStream = FileOutputStream(outFile)
            inputStream.copyTo(outputStream)
            inputStream.close()
            outputStream.close()
        } catch (e: Exception) {
            // Archivo inexistente o ignorado
        }
    }

    // --- HARDWARE BRIDGE ASYNC LOGIC ---

    fun fetchGpsLocation() {
        if (checkSelfPermission(android.Manifest.permission.ACCESS_FINE_LOCATION) != android.content.pm.PackageManager.PERMISSION_GRANTED) {
            requestPermissions(arrayOf(android.Manifest.permission.ACCESS_FINE_LOCATION, android.Manifest.permission.ACCESS_COARSE_LOCATION), 100)
        } else {
            readLocationAndCountDown()
        }
    }

    @SuppressLint("MissingPermission")
    private fun readLocationAndCountDown() {
        try {
            val lm = getSystemService(Context.LOCATION_SERVICE) as android.location.LocationManager
            val loc = lm.getLastKnownLocation(android.location.LocationManager.GPS_PROVIDER) 
                ?: lm.getLastKnownLocation(android.location.LocationManager.NETWORK_PROVIDER)
            if (loc != null) {
                gpsResult = "{\"lat\": ${loc.latitude}, \"lng\": ${loc.longitude}}"
            } else {
                gpsResult = "{\"error\": \"No location available\"}"
            }
        } catch (e: Exception) {
            gpsResult = "{\"error\": \"${e.message}\"}"
        }
        gpsLatch?.countDown()
    }

    override fun onRequestPermissionsResult(requestCode: Int, permissions: Array<out String>, grantResults: IntArray) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults)
        if (requestCode == 100) {
            if (grantResults.isNotEmpty() && grantResults[0] == android.content.pm.PackageManager.PERMISSION_GRANTED) {
                readLocationAndCountDown()
            } else {
                gpsResult = "{\"error\": \"Permission denied\"}"
                gpsLatch?.countDown()
            }
        }
        if (requestCode == 102) { // Camera permission
            if (grantResults.isNotEmpty() && grantResults[0] == android.content.pm.PackageManager.PERMISSION_GRANTED) {
                launchCameraIntent()
            } else {
                cameraResult = "{\"error\": \"Permission denied\"}"
                cameraLatch?.countDown()
            }
        }
        if (requestCode == 103) { // Notification permission
            if (grantResults.isNotEmpty() && grantResults[0] == android.content.pm.PackageManager.PERMISSION_GRANTED) {
                notificationResult = true
            } else {
                notificationResult = false
            }
            notificationLatch?.countDown()
        }
        if (requestCode == 104) { // Gallery permission
            if (grantResults.isNotEmpty() && grantResults[0] == android.content.pm.PackageManager.PERMISSION_GRANTED) {
                launchGalleryIntent()
            } else {
                galleryResult = "{\"error\": \"Permission denied\"}"
                galleryLatch?.countDown()
            }
        }
        if (requestCode == 105) { // Mic permission
            if (grantResults.isNotEmpty() && grantResults[0] == android.content.pm.PackageManager.PERMISSION_GRANTED) {
                micResult = true
            } else {
                micResult = false
            }
            micLatch?.countDown()
        }
        if (requestCode == 106) { // Contacts permission
            if (grantResults.isNotEmpty() && grantResults[0] == android.content.pm.PackageManager.PERMISSION_GRANTED) {
                readContactsAndCountDown()
            } else {
                contactsResult = "{\"error\": \"Permission denied\"}"
                contactsLatch?.countDown()
            }
        }
    }

    fun fetchCameraPicture() {
        if (checkSelfPermission(android.Manifest.permission.CAMERA) != android.content.pm.PackageManager.PERMISSION_GRANTED) {
            requestPermissions(arrayOf(android.Manifest.permission.CAMERA), 102)
        } else {
            launchCameraIntent()
        }
    }

    private fun launchCameraIntent() {
        try {
            val intent = Intent(android.provider.MediaStore.ACTION_IMAGE_CAPTURE)
            startActivityForResult(intent, 101)
        } catch (e: Exception) {
            cameraResult = "{\"error\": \"Camera not available\"}"
            cameraLatch?.countDown()
        }
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (requestCode == 101) {
            if (resultCode == Activity.RESULT_OK) {
                val imageBitmap = data?.extras?.get("data") as? android.graphics.Bitmap
                if (imageBitmap != null) {
                    val stream = java.io.ByteArrayOutputStream()
                    imageBitmap.compress(android.graphics.Bitmap.CompressFormat.JPEG, 80, stream)
                    val base64 = android.util.Base64.encodeToString(stream.toByteArray(), android.util.Base64.NO_WRAP)
                    cameraResult = "{\"base64\": \"$base64\"}"
                } else {
                    cameraResult = "{\"error\": \"No image data\"}"
                }
            } else {
                cameraResult = "{\"error\": \"User cancelled\"}"
            }
            cameraLatch?.countDown()
        }
        if (requestCode == 201) { // Gallery Intent
            if (resultCode == Activity.RESULT_OK && data != null && data.data != null) {
                try {
                    val uri = data.data!!
                    val inputStream = contentResolver.openInputStream(uri)
                    val bytes = inputStream?.readBytes()
                    inputStream?.close()
                    if (bytes != null) {
                        val base64 = android.util.Base64.encodeToString(bytes, android.util.Base64.NO_WRAP)
                        galleryResult = "{\"base64\": \"$base64\"}"
                    } else {
                        galleryResult = "{\"error\": \"Failed to read image\"}"
                    }
                } catch (e: Exception) {
                    galleryResult = "{\"error\": \"${e.message}\"}"
                }
            } else {
                galleryResult = "{\"error\": \"User cancelled\"}"
            }
            galleryLatch?.countDown()
        }
        if (requestCode == 202) { // File Picker Intent
            if (resultCode == Activity.RESULT_OK && data != null && data.data != null) {
                try {
                    val uri = data.data!!
                    var fileName = "file"
                    val cursor = contentResolver.query(uri, null, null, null, null)
                    cursor?.use {
                        if (it.moveToFirst()) {
                            val nameIndex = it.getColumnIndex(android.provider.OpenableColumns.DISPLAY_NAME)
                            if (nameIndex >= 0) fileName = it.getString(nameIndex)
                        }
                    }
                    val inputStream = contentResolver.openInputStream(uri)
                    val bytes = inputStream?.readBytes()
                    inputStream?.close()
                    if (bytes != null) {
                        val base64 = android.util.Base64.encodeToString(bytes, android.util.Base64.NO_WRAP)
                        val escapedName = fileName.replace("\"", "\\\"")
                        filePickerResult = "{\"filename\": \"$escapedName\", \"base64\": \"$base64\"}"
                    } else {
                        filePickerResult = "{\"error\": \"Failed to read file\"}"
                    }
                } catch (e: Exception) {
                    filePickerResult = "{\"error\": \"${e.message}\"}"
                }
            } else {
                filePickerResult = "{\"error\": \"User cancelled\"}"
            }
            filePickerLatch?.countDown()
        }
    }

    fun fetchNotificationPermission() {
        if (android.os.Build.VERSION.SDK_INT >= 33) {
            if (checkSelfPermission(android.Manifest.permission.POST_NOTIFICATIONS) != android.content.pm.PackageManager.PERMISSION_GRANTED) {
                requestPermissions(arrayOf(android.Manifest.permission.POST_NOTIFICATIONS), 103)
                return
            }
        }
        notificationResult = true
        notificationLatch?.countDown()
    }

    // --- NUEVAS APIS DE HARDWARE ---

    fun fetchBiometric(reason: String) {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.P) {
            val prompt = android.hardware.biometrics.BiometricPrompt.Builder(this)
                .setTitle("Phphone")
                .setSubtitle(reason)
                .setNegativeButton("Cancelar", mainExecutor, { _, _ -> 
                    biometricResult = false
                    biometricLatch?.countDown()
                })
                .build()

            val cancelSignal = android.os.CancellationSignal()
            prompt.authenticate(cancelSignal, mainExecutor, object : android.hardware.biometrics.BiometricPrompt.AuthenticationCallback() {
                override fun onAuthenticationSucceeded(result: android.hardware.biometrics.BiometricPrompt.AuthenticationResult?) {
                    super.onAuthenticationSucceeded(result)
                    biometricResult = true
                    biometricLatch?.countDown()
                }

                override fun onAuthenticationError(errorCode: Int, errString: CharSequence?) {
                    super.onAuthenticationError(errorCode, errString)
                    biometricResult = false
                    biometricLatch?.countDown()
                }

                override fun onAuthenticationFailed() {
                    super.onAuthenticationFailed()
                    biometricResult = false
                    biometricLatch?.countDown()
                }
            })
        } else {
            // No soportado, dejar pasar
            biometricResult = true
            biometricLatch?.countDown()
        }
    }

    fun fetchGalleryPicture() {
        if (Build.VERSION.SDK_INT <= Build.VERSION_CODES.S_V2) {
            if (checkSelfPermission(android.Manifest.permission.READ_EXTERNAL_STORAGE) != android.content.pm.PackageManager.PERMISSION_GRANTED) {
                requestPermissions(arrayOf(android.Manifest.permission.READ_EXTERNAL_STORAGE), 104)
                return
            }
        }
        launchGalleryIntent()
    }

    private fun launchGalleryIntent() {
        try {
            val intent = Intent(Intent.ACTION_GET_CONTENT)
            intent.type = "image/*"
            startActivityForResult(intent, 201)
        } catch (e: Exception) {
            galleryResult = "{\"error\": \"Gallery not available\"}"
            galleryLatch?.countDown()
        }
    }

    fun shareText(text: String, url: String) {
        val sendIntent = Intent().apply {
            action = Intent.ACTION_SEND
            putExtra(Intent.EXTRA_TEXT, if (url.isNotEmpty()) "$text\n$url" else text)
            type = "text/plain"
        }
        val shareIntent = Intent.createChooser(sendIntent, "Compartir")
        startActivity(shareIntent)
    }

    fun toggleFlashlight(on: Boolean) {
        try {
            val cameraManager = getSystemService(Context.CAMERA_SERVICE) as android.hardware.camera2.CameraManager
            val cameraId = cameraManager.cameraIdList[0]
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
                cameraManager.setTorchMode(cameraId, on)
            }
        } catch (e: Exception) {
            // No flash or error
        }
    }

    fun getClipboard(): String {
        val clipboard = getSystemService(Context.CLIPBOARD_SERVICE) as android.content.ClipboardManager
        return clipboard.primaryClip?.getItemAt(0)?.text?.toString() ?: ""
    }

    fun setClipboard(text: String) {
        val clipboard = getSystemService(Context.CLIPBOARD_SERVICE) as android.content.ClipboardManager
        val clip = android.content.ClipData.newPlainText("Phphone", text)
        clipboard.setPrimaryClip(clip)
    }

    // --- NUEVAS APIS FASE 5 ---

    fun secureWrite(key: String, value: String): Boolean {
        return try {
            val prefs = getSharedPreferences("phphone_secure_prefs", Context.MODE_PRIVATE)
            prefs.edit().putString(key, value).apply()
            true
        } catch (e: Exception) {
            false
        }
    }

    fun secureRead(key: String): String? {
        return try {
            val prefs = getSharedPreferences("phphone_secure_prefs", Context.MODE_PRIVATE)
            prefs.getString(key, null)
        } catch (e: Exception) {
            null
        }
    }

    fun fetchMicPermission() {
        if (checkSelfPermission(android.Manifest.permission.RECORD_AUDIO) != android.content.pm.PackageManager.PERMISSION_GRANTED) {
            requestPermissions(arrayOf(android.Manifest.permission.RECORD_AUDIO), 105)
        } else {
            micResult = true
            micLatch?.countDown()
        }
    }

    fun startAudioRecording(): Boolean {
        if (checkSelfPermission(android.Manifest.permission.RECORD_AUDIO) != android.content.pm.PackageManager.PERMISSION_GRANTED) {
            return false
        }
        return try {
            audioRecordPath = "${cacheDir.absolutePath}/phphone_audio_record.m4a"
            audioRecorder = if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.S) {
                android.media.MediaRecorder(this)
            } else {
                @Suppress("DEPRECATION")
                android.media.MediaRecorder()
            }
            
            audioRecorder?.setAudioSource(android.media.MediaRecorder.AudioSource.MIC)
            audioRecorder?.setOutputFormat(android.media.MediaRecorder.OutputFormat.MPEG_4)
            audioRecorder?.setAudioEncoder(android.media.MediaRecorder.AudioEncoder.AAC)
            audioRecorder?.setOutputFile(audioRecordPath)
            audioRecorder?.prepare()
            audioRecorder?.start()
            true
        } catch (e: Exception) {
            android.util.Log.e("Phphone", "Error starting audio record: ${e.message}")
            false
        }
    }

    fun stopAudioRecording(): String? {
        return try {
            audioRecorder?.stop()
            audioRecorder?.release()
            audioRecorder = null
            
            val file = File(audioRecordPath)
            if (file.exists()) {
                val bytes = file.readBytes()
                file.delete()
                android.util.Base64.encodeToString(bytes, android.util.Base64.NO_WRAP)
            } else {
                null
            }
        } catch (e: Exception) {
            android.util.Log.e("Phphone", "Error stopping audio record: ${e.message}")
            audioRecorder?.release()
            audioRecorder = null
            null
        }
    }

    fun fetchFilePicker() {
        try {
            val intent = Intent(Intent.ACTION_OPEN_DOCUMENT)
            intent.addCategory(Intent.CATEGORY_OPENABLE)
            intent.type = "*/*"
            startActivityForResult(intent, 202)
        } catch (e: Exception) {
            filePickerResult = "{\"error\": \"File picker not available\"}"
            filePickerLatch?.countDown()
        }
    }

    fun downloadFileToPublicDir(filename: String, base64: String): Boolean {
        return try {
            val bytes = android.util.Base64.decode(base64, android.util.Base64.DEFAULT)
            val downloadsDir = android.os.Environment.getExternalStoragePublicDirectory(android.os.Environment.DIRECTORY_DOWNLOADS)
            if (!downloadsDir.exists()) downloadsDir.mkdirs()
            
            val file = File(downloadsDir, filename)
            val fos = FileOutputStream(file)
            fos.write(bytes)
            fos.close()
            true
        } catch (e: Exception) {
            android.util.Log.e("Phphone", "Error downloading file: ${e.message}")
            false
        }
    }

    override fun onBackPressed() {
        if (webView.canGoBack()) {
            webView.goBack()
        } else {
            super.onBackPressed()
        }
    }

    fun startGyroscope() {
        if (isGyroRunning) return
        val sensorManager = getSystemService(android.content.Context.SENSOR_SERVICE) as android.hardware.SensorManager
        val gyroSensor = sensorManager.getDefaultSensor(android.hardware.Sensor.TYPE_GYROSCOPE)
        
        if (gyroSensor != null) {
            gyroSensorListener = object : android.hardware.SensorEventListener {
                override fun onSensorChanged(event: android.hardware.SensorEvent?) {
                    if (event != null) {
                        currentGyroX = event.values[0]
                        currentGyroY = event.values[1]
                        currentGyroZ = event.values[2]
                        // Ya no se apaga, actualiza variables en memoria RAM constantemente
                    }
                }
                override fun onAccuracyChanged(sensor: android.hardware.Sensor?, accuracy: Int) {}
            }
            sensorManager.registerListener(gyroSensorListener, gyroSensor, android.hardware.SensorManager.SENSOR_DELAY_GAME)
            isGyroRunning = true
            gyroscopeResult = "{\"success\": true}"
        } else {
            gyroscopeResult = "{\"error\": \"No gyroscope on this device\"}"
        }
    }

    fun stopGyroscope() {
        if (!isGyroRunning) return
        val sensorManager = getSystemService(android.content.Context.SENSOR_SERVICE) as android.hardware.SensorManager
        gyroSensorListener?.let {
            sensorManager.unregisterListener(it)
        }
        gyroSensorListener = null
        isGyroRunning = false
    }

    fun fetchGyroscope() {
        // En modo streaming, simplemente lee de memoria
        if (!isGyroRunning) {
            gyroscopeResult = "{\"error\": \"Gyroscope is not running. Call start first.\"}"
        } else {
            gyroscopeResult = "{\"x\": $currentGyroX, \"y\": $currentGyroY, \"z\": $currentGyroZ}"
        }
    }

    fun fetchContacts() {
        if (checkSelfPermission(android.Manifest.permission.READ_CONTACTS) != android.content.pm.PackageManager.PERMISSION_GRANTED) {
            requestPermissions(arrayOf(android.Manifest.permission.READ_CONTACTS), 106)
        } else {
            readContactsAndCountDown()
        }
    }

    @SuppressLint("Range")
    private fun readContactsAndCountDown() {
        Thread {
            try {
                val contactsList = mutableListOf<String>()
                val uri = android.provider.ContactsContract.CommonDataKinds.Phone.CONTENT_URI
                val cursor = contentResolver.query(uri, null, null, null, android.provider.ContactsContract.CommonDataKinds.Phone.DISPLAY_NAME + " ASC")
                
                cursor?.use {
                    while (it.moveToNext()) {
                        val name = it.getString(it.getColumnIndex(android.provider.ContactsContract.CommonDataKinds.Phone.DISPLAY_NAME))
                        val number = it.getString(it.getColumnIndex(android.provider.ContactsContract.CommonDataKinds.Phone.NUMBER))
                        
                        if (name != null && number != null) {
                            val escapedName = name.replace("\"", "\\\"").replace("\n", " ").trim()
                            val escapedNumber = number.replace("\"", "\\\"").replace("\n", " ").trim()
                            contactsList.add("{\"name\": \"$escapedName\", \"phone\": \"$escapedNumber\"}")
                        }
                    }
                }
                
                val jsonArray = "[" + contactsList.joinToString(",") + "]"
                contactsResult = jsonArray
            } catch (e: Exception) {
                contactsResult = "{\"error\": \"${e.message}\"}"
            }
            contactsLatch?.countDown()
        }.start()
    }
    
    /*
    // --- PUSH NOTIFICATIONS (PHPHONE) ---
    // Para activar Firebase, descomenta el siguiente método
    fun fetchPushToken() {
        try {
            com.google.firebase.messaging.FirebaseMessaging.getInstance().token.addOnCompleteListener { task ->
                if (!task.isSuccessful) {
                    pushTokenResult = "{\"error\": \"Fetching FCM registration token failed\"}"
                } else {
                    val token = task.result
                    val escapedToken = token?.replace("\"", "\\\"") ?: ""
                    pushTokenResult = "{\"token\": \"$escapedToken\"}"
                }
                pushTokenLatch?.countDown()
            }
        } catch (e: Exception) {
            pushTokenResult = "{\"error\": \"Firebase not configured: ${e.message}\"}"
            pushTokenLatch?.countDown()
        }
    }
    */

    /*
    // --- IN APP PURCHASES (PHPHONE) ---
    // Para activar Compras Integradas, descomenta este bloque e inicializa billingClient en onCreate():
    // billingClient = com.android.billingclient.api.BillingClient.newBuilder(this).enablePendingPurchases().setListener(purchasesUpdatedListener).build()
    
    // private val purchasesUpdatedListener = com.android.billingclient.api.PurchasesUpdatedListener { billingResult, purchases ->
    //     if (billingResult.responseCode == com.android.billingclient.api.BillingClient.BillingResponseCode.OK && purchases != null) {
    //         val purchaseToken = purchases.first().purchaseToken
    //         purchaseResult = "{\"success\": true, \"purchaseToken\": \"$purchaseToken\"}"
    //     } else {
    //         purchaseResult = "{\"error\": \"Purchase failed or cancelled\"}"
    //     }
    //     purchaseLatch?.countDown()
    // }

    // fun purchaseProduct(productId: String) {
    //     billingClient?.startConnection(object : com.android.billingclient.api.BillingClientStateListener {
    //         override fun onBillingSetupFinished(billingResult: com.android.billingclient.api.BillingResult) {
    //             if (billingResult.responseCode == com.android.billingclient.api.BillingClient.BillingResponseCode.OK) {
    //                 val productList = listOf(com.android.billingclient.api.QueryProductDetailsParams.Product.newBuilder()
    //                     .setProductId(productId)
    //                     .setProductType(com.android.billingclient.api.BillingClient.ProductType.INAPP)
    //                     .build())
    //                 val params = com.android.billingclient.api.QueryProductDetailsParams.newBuilder().setProductList(productList).build()
    //                 
    //                 billingClient?.queryProductDetailsAsync(params) { billingResult2, productDetailsList ->
    //                     if (productDetailsList.isNotEmpty()) {
    //                         val productDetails = productDetailsList.first()
    //                         val productDetailsParamsList = listOf(com.android.billingclient.api.BillingFlowParams.ProductDetailsParams.newBuilder().setProductDetails(productDetails).build())
    //                         val flowParams = com.android.billingclient.api.BillingFlowParams.newBuilder().setProductDetailsParamsList(productDetailsParamsList).build()
    //                         billingClient?.launchBillingFlow(this@MainActivity, flowParams)
    //                     } else {
    //                         purchaseResult = "{\"error\": \"Product not found\"}"
    //                         purchaseLatch?.countDown()
    //                     }
    //                 }
    //             } else {
    //                 purchaseResult = "{\"error\": \"Billing setup failed\"}"
    //                 purchaseLatch?.countDown()
    //             }
    //         }
    //         override fun onBillingServiceDisconnected() {
    //             purchaseResult = "{\"error\": \"Billing disconnected\"}"
    //             purchaseLatch?.countDown()
    //         }
    //     })
    // }
    */
}
