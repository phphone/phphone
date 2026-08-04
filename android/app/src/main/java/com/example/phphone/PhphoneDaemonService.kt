package com.example.phphone

import android.app.Notification
import android.app.NotificationChannel
import android.app.NotificationManager
import android.app.Service
import android.content.Context
import android.content.Intent
import android.os.Build
import android.os.IBinder
import android.util.Log
import java.net.HttpURLConnection
import java.net.URL

class PhphoneDaemonService : Service() {

    private val CHANNEL_ID = "PhphoneDaemonChannel"
    private var daemonThread: Thread? = null
    @Volatile private var isRunning = false

    override fun onCreate() {
        super.onCreate()
        createNotificationChannel()
    }

    override fun onStartCommand(intent: Intent?, flags: Int, startId: Int): Int {
        val taskName = intent?.getStringExtra("taskName") ?: "daemon"
        val interval = intent?.getIntExtra("interval", 60) ?: 60
        val endpoint = intent?.getStringExtra("endpoint") ?: "/daemon.php"

        val notification: Notification = if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            Notification.Builder(this, CHANNEL_ID)
                .setContentTitle("Phphone en ejecución")
                .setContentText("Procesando tareas en segundo plano...")
                .setSmallIcon(android.R.drawable.ic_menu_info_details) // Icono por defecto temporal
                .build()
        } else {
            @Suppress("DEPRECATION")
            Notification.Builder(this)
                .setContentTitle("Phphone en ejecución")
                .setContentText("Procesando tareas en segundo plano...")
                .setSmallIcon(android.R.drawable.ic_menu_info_details)
                .build()
        }

        startForeground(1, notification)

        // Iniciar el bucle del demonio
        isRunning = false
        daemonThread?.interrupt()
        
        isRunning = true
        daemonThread = Thread {
            while (isRunning) {
                try {
                    Log.i("PhphoneDaemon", "Ejecutando tarea: \$taskName en \$endpoint")
                    
                    // Hacer la petición al motor PHP local
                    val url = URL("http://127.0.0.1:8081\$endpoint?task=\$taskName")
                    val connection = url.openConnection() as HttpURLConnection
                    connection.requestMethod = "GET"
                    connection.connectTimeout = 5000
                    
                    val responseCode = connection.responseCode
                    Log.i("PhphoneDaemon", "Respuesta del motor PHP: \$responseCode")
                    connection.disconnect()
                } catch (e: Exception) {
                    Log.e("PhphoneDaemon", "Error llamando al motor PHP local (¿Está el servidor apagado?): \${e.message}")
                }
                
                try {
                    Thread.sleep(interval * 1000L)
                } catch (e: InterruptedException) {
                    // Hilo interrumpido, salir si isRunning cambió
                }
            }
        }
        daemonThread?.start()

        return START_STICKY
    }

    override fun onDestroy() {
        super.onDestroy()
        isRunning = false
        daemonThread?.interrupt()
    }

    override fun onBind(intent: Intent?): IBinder? {
        return null
    }

    private fun createNotificationChannel() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            val serviceChannel = NotificationChannel(
                CHANNEL_ID,
                "Phphone Background Service",
                NotificationManager.IMPORTANCE_LOW
            )
            val manager = getSystemService(NotificationManager::class.java)
            manager.createNotificationChannel(serviceChannel)
        }
    }
}
