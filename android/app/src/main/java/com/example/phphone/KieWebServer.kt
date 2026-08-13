package com.example.phphone

import android.content.Context
import android.os.Build
import android.os.Handler
import android.os.Looper
import android.os.VibrationEffect
import android.os.Vibrator
import android.os.VibratorManager
import android.widget.Toast
import fi.iki.elonen.NanoHTTPD

class KieWebServer(private val context: Context, port: Int) : NanoHTTPD("127.0.0.1", port) {

    override fun serve(session: IHTTPSession): Response {
        val uri = session.uri
        val params = session.parameters
        var response: Response

        try {
            when (uri) {
                "/api/vibrate" -> {
                    val ms = params["ms"]?.firstOrNull()?.toLongOrNull() ?: 500L
                    vibrateDevice(ms)
                    response = newFixedLengthResponse(Response.Status.OK, "application/json", "{\"success\":true}")
                }
                "/api/toast" -> {
                    val msg = params["msg"]?.firstOrNull() ?: "Mensaje vacío"
                    showToast(msg)
                    response = newFixedLengthResponse(Response.Status.OK, "application/json", "{\"success\":true}")
                }
                "/api/gps" -> {
                    val activity = context as MainActivity
                    activity.gpsLatch = java.util.concurrent.CountDownLatch(1)
                    
                    Handler(Looper.getMainLooper()).post {
                        activity.fetchGpsLocation()
                    }
                    
                    activity.gpsLatch?.await()
                    val resultJson = activity.gpsResult ?: "{\"error\":\"Unknown GPS error\"}"
                    response = newFixedLengthResponse(Response.Status.OK, "application/json", resultJson)
                }
                "/api/camera" -> {
                    val activity = context as MainActivity
                    activity.cameraLatch = java.util.concurrent.CountDownLatch(1)
                    
                    Handler(Looper.getMainLooper()).post {
                        activity.fetchCameraPicture()
                    }
                    
                    activity.cameraLatch?.await()
                    val resultJson = activity.cameraResult ?: "{\"error\":\"Unknown Camera error\"}"
                    response = newFixedLengthResponse(Response.Status.OK, "application/json", resultJson)
                }
                "/api/request-notification-permission" -> {
                    val activity = context as MainActivity
                    activity.notificationLatch = java.util.concurrent.CountDownLatch(1)
                    
                    Handler(Looper.getMainLooper()).post {
                        activity.fetchNotificationPermission()
                    }
                    
                    activity.notificationLatch?.await()
                    val granted = activity.notificationResult
                    response = newFixedLengthResponse(Response.Status.OK, "application/json", "{\"success\":$granted}")
                }
                "/api/notification" -> {
                    val activity = context as MainActivity
                    activity.notificationLatch = java.util.concurrent.CountDownLatch(1)
                    
                    Handler(Looper.getMainLooper()).post {
                        activity.fetchNotificationPermission()
                    }
                    
                    activity.notificationLatch?.await()
                    
                    if (activity.notificationResult) {
                        val title = params["title"]?.firstOrNull() ?: "Phphone"
                        val msg = params["msg"]?.firstOrNull() ?: "Notificación"
                        showNotification(title, msg)
                        response = newFixedLengthResponse(Response.Status.OK, "application/json", "{\"success\":true}")
                    } else {
                        response = newFixedLengthResponse(Response.Status.OK, "application/json", "{\"success\":false, \"error\":\"Permission denied\"}")
                    }
                }
                "/api/metrics" -> {
                    val activityManager = context.getSystemService(Context.ACTIVITY_SERVICE) as android.app.ActivityManager
                    val memoryInfo = android.app.ActivityManager.MemoryInfo()
                    activityManager.getMemoryInfo(memoryInfo)
                    
                    val totalRam = memoryInfo.totalMem / (1024 * 1024)
                    val availRam = memoryInfo.availMem / (1024 * 1024)
                    val usedRam = totalRam - availRam
                    
                    var cpuUsage = 0.0
                    try {
                        cpuUsage = Runtime.getRuntime().availableProcessors() * 10.0 // Mock fallback
                    } catch (e: Exception) {}
                    
                    val cpuStr = String.format(java.util.Locale.US, "%.1f", cpuUsage)
                    val json = "{\"success\":true, \"ram_used\": $usedRam, \"ram_total\": $totalRam, \"cpu\": $cpuStr}"
                    response = newFixedLengthResponse(Response.Status.OK, "application/json", json)
                }
                // --- NUEVAS APIS ---
                "/api/biometric" -> {
                    val activity = context as MainActivity
                    val reason = params["reason"]?.firstOrNull() ?: "Confirma tu identidad"
                    activity.biometricLatch = java.util.concurrent.CountDownLatch(1)
                    
                    Handler(Looper.getMainLooper()).post {
                        activity.fetchBiometric(reason)
                    }
                    
                    activity.biometricLatch?.await()
                    if (activity.biometricResult) {
                        response = newFixedLengthResponse(Response.Status.OK, "application/json", "{\"success\":true}")
                    } else {
                        response = newFixedLengthResponse(Response.Status.OK, "application/json", "{\"success\":false, \"error\":\"Biometric failed or cancelled\"}")
                    }
                }
                "/api/gyroscope/start" -> {
                    val activity = context as MainActivity
                    Handler(Looper.getMainLooper()).post { activity.startGyroscope() }
                    response = newFixedLengthResponse(Response.Status.OK, "application/json", "{\"success\":true}")
                }
                "/api/gyroscope/stop" -> {
                    val activity = context as MainActivity
                    Handler(Looper.getMainLooper()).post { activity.stopGyroscope() }
                    response = newFixedLengthResponse(Response.Status.OK, "application/json", "{\"success\":true}")
                }
                "/api/gyroscope" -> {
                    val activity = context as MainActivity
                    activity.fetchGyroscope()
                    val resultJson = activity.gyroscopeResult ?: "{\"error\":\"Unknown Gyroscope error\"}"
                    response = newFixedLengthResponse(Response.Status.OK, "application/json", resultJson)
                }
                "/api/contacts" -> {
                    val activity = context as MainActivity
                    activity.contactsLatch = java.util.concurrent.CountDownLatch(1)
                    
                    Handler(Looper.getMainLooper()).post {
                        activity.fetchContacts()
                    }
                    
                    activity.contactsLatch?.await()
                    val resultJson = activity.contactsResult ?: "{\"error\":\"Unknown Contacts error\"}"
                    response = newFixedLengthResponse(Response.Status.OK, "application/json", resultJson)
                }
                "/api/gallery" -> {
                    val activity = context as MainActivity
                    activity.galleryLatch = java.util.concurrent.CountDownLatch(1)
                    
                    Handler(Looper.getMainLooper()).post {
                        activity.fetchGalleryPicture()
                    }
                    
                    activity.galleryLatch?.await()
                    val resultJson = activity.galleryResult ?: "{\"error\":\"Unknown Gallery error\"}"
                    response = newFixedLengthResponse(Response.Status.OK, "application/json", resultJson)
                }
                // --- PUSH NOTIFICATIONS (PHPHONE) ---
                "/api/push_token" -> {
                    val activity = context as MainActivity
                    activity.pushTokenLatch = java.util.concurrent.CountDownLatch(1)
                    Handler(Looper.getMainLooper()).post { activity.fetchPushToken() }
                    activity.pushTokenLatch?.await()
                    val resultJson = activity.pushTokenResult ?: "{\"error\":\"Unknown Push Token error\"}"
                    response = newFixedLengthResponse(Response.Status.OK, "application/json", resultJson)
                }
                /*
                // --- IN APP PURCHASES (PHPHONE) ---
                "/api/iap/purchase" -> {
                    val productId = params["productId"]?.firstOrNull() ?: ""
                    if (productId.isNotEmpty()) {
                        val activity = context as MainActivity
                        activity.purchaseLatch = java.util.concurrent.CountDownLatch(1)
                        Handler(Looper.getMainLooper()).post { activity.purchaseProduct(productId) }
                        activity.purchaseLatch?.await()
                        val resultJson = activity.purchaseResult ?: "{\"error\":\"Unknown Purchase error\"}"
                        response = newFixedLengthResponse(Response.Status.OK, "application/json", resultJson)
                    } else {
                        response = newFixedLengthResponse(Response.Status.OK, "application/json", "{\"error\":\"Missing productId\"}")
                    }
                }
                */
                "/api/share" -> {
                    val activity = context as MainActivity
                    val text = params["text"]?.firstOrNull() ?: ""
                    val url = params["url"]?.firstOrNull() ?: ""
                    
                    Handler(Looper.getMainLooper()).post {
                        activity.shareText(text, url)
                    }
                    response = newFixedLengthResponse(Response.Status.OK, "application/json", "{\"success\":true}")
                }
                "/api/battery" -> {
                    val bm = context.getSystemService(Context.BATTERY_SERVICE) as android.os.BatteryManager
                    val level = bm.getIntProperty(android.os.BatteryManager.BATTERY_PROPERTY_CAPACITY)
                    val intentFilter = android.content.IntentFilter(android.content.Intent.ACTION_BATTERY_CHANGED)
                    val batteryStatus = context.registerReceiver(null, intentFilter)
                    val status = batteryStatus?.getIntExtra(android.os.BatteryManager.EXTRA_STATUS, -1) ?: -1
                    val isCharging = status == android.os.BatteryManager.BATTERY_STATUS_CHARGING || status == android.os.BatteryManager.BATTERY_STATUS_FULL
                    
                    val json = "{\"success\":true, \"level\": $level, \"isCharging\": $isCharging}"
                    response = newFixedLengthResponse(Response.Status.OK, "application/json", json)
                }
                "/api/network" -> {
                    val cm = context.getSystemService(Context.CONNECTIVITY_SERVICE) as android.net.ConnectivityManager
                    val activeNetwork = cm.activeNetwork
                    val networkCapabilities = cm.getNetworkCapabilities(activeNetwork)
                    var status = "offline"
                    if (networkCapabilities != null) {
                        if (networkCapabilities.hasTransport(android.net.NetworkCapabilities.TRANSPORT_WIFI)) {
                            status = "wifi"
                        } else if (networkCapabilities.hasTransport(android.net.NetworkCapabilities.TRANSPORT_CELLULAR)) {
                            status = "cellular"
                        } else {
                            status = "unknown"
                        }
                    }
                    response = newFixedLengthResponse(Response.Status.OK, "application/json", "{\"success\":true, \"status\":\"$status\"}")
                }
                "/api/clipboard" -> {
                    val activity = context as MainActivity
                    session.parseBody(HashMap<String, String>())
                    val postData = session.parameters
                    
                    if (session.method == Method.POST) {
                        val textToCopy = postData["text"]?.firstOrNull() ?: ""
                        Handler(Looper.getMainLooper()).post {
                            activity.setClipboard(textToCopy)
                        }
                        response = newFixedLengthResponse(Response.Status.OK, "application/json", "{\"success\":true}")
                    } else {
                        var clipText = ""
                        val latch = java.util.concurrent.CountDownLatch(1)
                        Handler(Looper.getMainLooper()).post {
                            clipText = activity.getClipboard()
                            latch.countDown()
                        }
                        latch.await()
                        
                        // Escapar comillas dobles y diagonales en el texto recuperado
                        val escaped = clipText.replace("\\", "\\\\").replace("\"", "\\\"").replace("\n", "\\n").replace("\r", "")
                        response = newFixedLengthResponse(Response.Status.OK, "application/json", "{\"success\":true, \"text\":\"$escaped\"}")
                    }
                }
                "/api/flashlight" -> {
                    val activity = context as MainActivity
                    val onParam = params["on"]?.firstOrNull() ?: "true"
                    val turnOn = onParam == "true"
                    Handler(Looper.getMainLooper()).post {
                        activity.toggleFlashlight(turnOn)
                    }
                    response = newFixedLengthResponse(Response.Status.OK, "application/json", "{\"success\":true}")
                }
                "/api/info" -> {
                    val model = Build.MODEL
                    val osVersion = Build.VERSION.RELEASE
                    val uuid = android.provider.Settings.Secure.getString(context.contentResolver, android.provider.Settings.Secure.ANDROID_ID) ?: "unknown"
                    val json = "{\"success\":true, \"model\": \"$model\", \"os_version\": \"$osVersion\", \"uuid\": \"$uuid\"}"
                    response = newFixedLengthResponse(Response.Status.OK, "application/json", json)
                }
                "/api/audio/play" -> {
                    val activity = context as MainActivity
                    val path = params["path"]?.firstOrNull() ?: ""
                    val loop = params["loop"]?.firstOrNull() == "true"
                    Handler(Looper.getMainLooper()).post {
                        activity.playNativeAudio(path, loop)
                    }
                    response = newFixedLengthResponse(Response.Status.OK, "application/json", "{\"success\":true}")
                }
                "/api/audio/stop" -> {
                    val activity = context as MainActivity
                    Handler(Looper.getMainLooper()).post {
                        activity.stopNativeAudio()
                    }
                    response = newFixedLengthResponse(Response.Status.OK, "application/json", "{\"success\":true}")
                }
                "/api/secure/write" -> {
                    val activity = context as MainActivity
                    session.parseBody(HashMap<String, String>())
                    val key = session.parameters["key"]?.firstOrNull() ?: ""
                    val value = session.parameters["value"]?.firstOrNull() ?: ""
                    val success = activity.secureWrite(key, value)
                    response = newFixedLengthResponse(Response.Status.OK, "application/json", "{\"success\":$success}")
                }
                "/api/secure/read" -> {
                    val activity = context as MainActivity
                    val key = params["key"]?.firstOrNull() ?: ""
                    val value = activity.secureRead(key)
                    if (value != null) {
                        val escaped = value.replace("\\", "\\\\").replace("\"", "\\\"").replace("\n", "\\n").replace("\r", "")
                        response = newFixedLengthResponse(Response.Status.OK, "application/json", "{\"value\":\"$escaped\"}")
                    } else {
                        response = newFixedLengthResponse(Response.Status.OK, "application/json", "{\"error\":\"Not found\"}")
                    }
                }
                "/api/openurl" -> {
                    val activity = context as MainActivity
                    val url = params["url"]?.firstOrNull() ?: ""
                    Handler(Looper.getMainLooper()).post {
                        try {
                            val intent = android.content.Intent(android.content.Intent.ACTION_VIEW, android.net.Uri.parse(url))
                            activity.startActivity(intent)
                        } catch (e: Exception) {}
                    }
                    response = newFixedLengthResponse(Response.Status.OK, "application/json", "{\"success\":true}")
                }
                "/api/inappbrowser" -> {
                    val activity = context as MainActivity
                    val url = params["url"]?.firstOrNull() ?: ""
                    Handler(Looper.getMainLooper()).post {
                        try {
                            val intent = android.content.Intent(android.content.Intent.ACTION_VIEW, android.net.Uri.parse(url))
                            activity.startActivity(intent)
                        } catch (e: Exception) {}
                    }
                    response = newFixedLengthResponse(Response.Status.OK, "application/json", "{\"success\":true}")
                }
                "/api/mic/start" -> {
                    val activity = context as MainActivity
                    activity.micLatch = java.util.concurrent.CountDownLatch(1)
                    Handler(Looper.getMainLooper()).post {
                        activity.fetchMicPermission()
                    }
                    activity.micLatch?.await()
                    if (activity.micResult) {
                        val started = activity.startAudioRecording()
                        response = newFixedLengthResponse(Response.Status.OK, "application/json", "{\"success\":$started}")
                    } else {
                        response = newFixedLengthResponse(Response.Status.OK, "application/json", "{\"success\":false, \"error\":\"Permission denied\"}")
                    }
                }
                "/api/mic/stop" -> {
                    val activity = context as MainActivity
                    val base64 = activity.stopAudioRecording()
                    if (base64 != null) {
                        response = newFixedLengthResponse(Response.Status.OK, "application/json", "{\"base64\":\"$base64\"}")
                    } else {
                        response = newFixedLengthResponse(Response.Status.OK, "application/json", "{\"error\":\"Failed to save audio\"}")
                    }
                }
                "/api/filepicker" -> {
                    val activity = context as MainActivity
                    activity.filePickerLatch = java.util.concurrent.CountDownLatch(1)
                    Handler(Looper.getMainLooper()).post {
                        activity.fetchFilePicker()
                    }
                    activity.filePickerLatch?.await()
                    val resultJson = activity.filePickerResult ?: "{\"error\":\"Unknown File Picker error\"}"
                    response = newFixedLengthResponse(Response.Status.OK, "application/json", resultJson)
                }
                "/api/file/download" -> {
                    val activity = context as MainActivity
                    session.parseBody(HashMap<String, String>())
                    val filename = session.parameters["filename"]?.firstOrNull() ?: "file.bin"
                    val base64 = session.parameters["base64"]?.firstOrNull() ?: ""
                    val success = activity.downloadFileToPublicDir(filename, base64)
                    response = newFixedLengthResponse(Response.Status.OK, "application/json", "{\"success\":$success}")
                }
                else -> {
                    response = newFixedLengthResponse(Response.Status.NOT_FOUND, "application/json", "{\"error\":\"Endpoint not found\"}")
                }
            }
        } catch (e: Exception) {
            response = newFixedLengthResponse(Response.Status.INTERNAL_ERROR, "application/json", "{\"error\":\"${e.message}\"}")
        }

        response.addHeader("Access-Control-Allow-Origin", "*")
        return response
    }

    private fun vibrateDevice(milliseconds: Long) {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.S) {
            val vibratorManager = context.getSystemService(Context.VIBRATOR_MANAGER_SERVICE) as VibratorManager
            val vibrator = vibratorManager.defaultVibrator
            vibrator.vibrate(VibrationEffect.createOneShot(milliseconds, VibrationEffect.DEFAULT_AMPLITUDE))
        } else {
            @Suppress("DEPRECATION")
            val vibrator = context.getSystemService(Context.VIBRATOR_SERVICE) as Vibrator
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
                vibrator.vibrate(VibrationEffect.createOneShot(milliseconds, VibrationEffect.DEFAULT_AMPLITUDE))
            } else {
                @Suppress("DEPRECATION")
                vibrator.vibrate(milliseconds)
            }
        }
    }

    private fun showToast(message: String) {
        Handler(Looper.getMainLooper()).post {
            Toast.makeText(context, message, Toast.LENGTH_SHORT).show()
        }
    }

    private fun showNotification(title: String, message: String) {
        val manager = context.getSystemService(Context.NOTIFICATION_SERVICE) as android.app.NotificationManager
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            val channel = android.app.NotificationChannel("kie_channel", "Phphone Notifications", android.app.NotificationManager.IMPORTANCE_DEFAULT)
            manager.createNotificationChannel(channel)
        }

        val builder = if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            android.app.Notification.Builder(context, "kie_channel")
        } else {
            @Suppress("DEPRECATION")
            android.app.Notification.Builder(context)
        }

        val notification = builder
            .setContentTitle(title)
            .setContentText(message)
            .setSmallIcon(android.R.drawable.ic_dialog_info)
            .setAutoCancel(true)
            .build()

        manager.notify((System.currentTimeMillis() % 10000).toInt(), notification)
    }
}
