package com.example.phphone

import android.app.NotificationChannel
import android.app.NotificationManager
import android.app.PendingIntent
import android.content.Context
import android.content.Intent
import android.media.RingtoneManager
import android.os.Build
import androidx.core.app.NotificationCompat
import androidx.core.app.RemoteInput
import com.google.firebase.messaging.FirebaseMessagingService
import com.google.firebase.messaging.RemoteMessage

class KieFirebaseMessagingService : FirebaseMessagingService() {

    override fun onMessageReceived(remoteMessage: RemoteMessage) {
        val title = remoteMessage.notification?.title ?: remoteMessage.data["title"]
        val body = remoteMessage.notification?.body ?: remoteMessage.data["body"]
        val tag = remoteMessage.data["tag"] ?: remoteMessage.data["id"] ?: remoteMessage.data["notification_id"]
        val group = remoteMessage.data["group"] ?: remoteMessage.data["thread_id"]
        val route = remoteMessage.data["route"] ?: remoteMessage.data["url"]
        val allowReply = remoteMessage.data["reply"] == "true" || remoteMessage.data["reply"] == "1"

        if (title != null || body != null) {
            sendNotification(title, body, tag, group, route, allowReply)
        }
    }

    override fun onNewToken(token: String) {
        // Firebase asignó un nuevo token al dispositivo.
        println("FCM New Token: $token")
    }

    private fun sendNotification(
        title: String?,
        messageBody: String?,
        tag: String? = null,
        group: String? = null,
        route: String? = null,
        allowReply: Boolean = false
    ) {
        val intent = Intent(this, MainActivity::class.java).apply {
            addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP or Intent.FLAG_ACTIVITY_SINGLE_TOP)
            if (route != null) {
                putExtra("route", route)
            }
        }
        val pendingIntent = PendingIntent.getActivity(
            this,
            0,
            intent,
            PendingIntent.FLAG_IMMUTABLE or PendingIntent.FLAG_UPDATE_CURRENT
        )

        val channelId = "phphone_default_channel"
        val defaultSoundUri = RingtoneManager.getDefaultUri(RingtoneManager.TYPE_NOTIFICATION)
        val notificationBuilder = NotificationCompat.Builder(this, channelId)
            .setSmallIcon(R.mipmap.ic_launcher)
            .setContentTitle(title ?: "Notificación")
            .setContentText(messageBody)
            .setAutoCancel(true)
            .setSound(defaultSoundUri)
            .setContentIntent(pendingIntent)

        if (!group.isNullOrEmpty()) {
            notificationBuilder.setGroup(group)
        }

        // Si se habilita la respuesta directa (Direct Reply)
        if (allowReply) {
            val KEY_TEXT_REPLY = "key_text_reply"
            val remoteInput = RemoteInput.Builder(KEY_TEXT_REPLY)
                .setLabel("Escribe una respuesta...")
                .build()

            val replyFlags = if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.S) {
                PendingIntent.FLAG_MUTABLE or PendingIntent.FLAG_UPDATE_CURRENT
            } else {
                PendingIntent.FLAG_UPDATE_CURRENT
            }

            val replyPendingIntent = PendingIntent.getActivity(
                this,
                (System.currentTimeMillis() % 100000).toInt(),
                intent,
                replyFlags
            )

            val replyAction = NotificationCompat.Action.Builder(
                R.mipmap.ic_launcher,
                "Responder",
                replyPendingIntent
            )
                .addRemoteInput(remoteInput)
                .build()

            notificationBuilder.addAction(replyAction)
        }

        val notificationManager = getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager

        // Crear el canal de notificaciones (requerido a partir de Android Oreo)
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            val channel = NotificationChannel(
                channelId,
                "Phphone Notifications",
                NotificationManager.IMPORTANCE_DEFAULT
            )
            notificationManager.createNotificationChannel(channel)
        }

        val notificationId = if (!tag.isNullOrEmpty()) {
            tag.hashCode()
        } else {
            (System.currentTimeMillis() % 100000).toInt()
        }

        notificationManager.notify(notificationId, notificationBuilder.build())

        // Si pertenece a un grupo, crear/actualizar la notificación resumen (Group Summary)
        if (!group.isNullOrEmpty() && Build.VERSION.SDK_INT >= Build.VERSION_CODES.N) {
            val summaryNotification = NotificationCompat.Builder(this, channelId)
                .setSmallIcon(R.mipmap.ic_launcher)
                .setStyle(NotificationCompat.InboxStyle().setSummaryText(title ?: "Phphone Notifications"))
                .setGroup(group)
                .setGroupSummary(true)
                .setAutoCancel(true)
                .build()

            val summaryId = group.hashCode()
            notificationManager.notify(summaryId, summaryNotification)
        }
    }
}
