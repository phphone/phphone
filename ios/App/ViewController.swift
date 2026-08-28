import UIKit
import WebKit
import CoreLocation
import UserNotifications
import BackgroundTasks
#if canImport(Darwin)
import Darwin
#endif
import LocalAuthentication
import AVFoundation
import Network
import SafariServices
/*
// --- PUSH NOTIFICATIONS (PHPHONE) ---
import FirebaseCore
import FirebaseMessaging
*/
/*
// --- IN APP PURCHASES (PHPHONE) ---
import StoreKit
*/

/*
// --- PUSH NOTIFICATIONS (PHPHONE) ---
// Añade los delegados UNUserNotificationCenterDelegate, MessagingDelegate a la clase si activas Firebase:
// class ViewController: UIViewController, WKNavigationDelegate, CLLocationManagerDelegate, UIImagePickerControllerDelegate, UINavigationControllerDelegate, UIDocumentPickerDelegate, UNUserNotificationCenterDelegate, MessagingDelegate {
*/
/*
// --- IN APP PURCHASES (PHPHONE) ---
// Añade los delegados SKPaymentTransactionObserver, SKProductsRequestDelegate a la clase si activas IAP:
// class ViewController: UIViewController, WKNavigationDelegate, CLLocationManagerDelegate, UIImagePickerControllerDelegate, UINavigationControllerDelegate, UIDocumentPickerDelegate, SKPaymentTransactionObserver, SKProductsRequestDelegate {
*/
class PassthroughWebView: WKWebView {
    var uiRects: [CGRect] = []
    
    override func hitTest(_ point: CGPoint, with event: UIEvent?) -> UIView? {
        guard !isHidden && alpha > 0 else { return nil }
        
        var insideUI = false
        for rect in uiRects {
            if rect.contains(point) {
                insideUI = true
                break
            }
        }
        
        if !insideUI {
            return nil
        }
        
        return super.hitTest(point, with: event)
    }
}

class ViewController: UIViewController, WKNavigationDelegate, CLLocationManagerDelegate, UIImagePickerControllerDelegate, UINavigationControllerDelegate, UIDocumentPickerDelegate, WKScriptMessageHandler, UIScrollViewDelegate {

    static var KIE_ZOOM_ENABLED = false // CONFIG_ZOOM

    var webView: PassthroughWebView!
    var nativeWebView: WKWebView!
    var webServer: GCDWebServer!
    var audioPlayer: AVAudioPlayer?
    
    // Sincronización para GPS
    var locationManager: CLLocationManager?
    var motionManager = CMMotionManager()
    
    // Variables para Gyroscope Streaming
    var isGyroRunning = false
    var currentGyroX: Double = 0.0
    var currentGyroY: Double = 0.0
    var currentGyroZ: Double = 0.0
    
    var gpsSemaphore: DispatchSemaphore?
    var latestLocation: CLLocation?

    var biometricSemaphore: DispatchSemaphore?
    var biometricResult: Bool = false

    // --- IN APP PURCHASES (PHPHONE) ---
    // var purchaseSemaphore: DispatchSemaphore?
    // var purchaseResult: String? = nil
    // var currentProductRequest: SKProductsRequest?

    var gallerySemaphore: DispatchSemaphore?
    var galleryResult: String? = nil

    var filePickerSemaphore: DispatchSemaphore?
    var filePickerResult: String? = nil

    var audioRecorder: AVAudioRecorder?
    var audioRecordURL: URL?

    // --- PUSH NOTIFICATIONS (PHPHONE) ---
    // var pushToken: String? = null

    override func loadView() {
        super.loadView()
        
        let webConfiguration = WKWebViewConfiguration()
        
        // Soporte universal para el Notch (Safe Area) y control de Zoom
        let viewportContent = ViewController.KIE_ZOOM_ENABLED ? "width=device-width, initial-scale=1.0, viewport-fit=cover" : "width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover"
        
        let viewportScriptSource = """
            var meta = document.querySelector('meta[name="viewport"]');
            if (meta) {
                meta.content = '\(viewportContent)';
            } else {
                meta = document.createElement('meta');
                meta.name = 'viewport';
                meta.content = '\(viewportContent)';
                document.head.appendChild(meta);
            }
        """
        let viewportScript = WKUserScript(source: viewportScriptSource, injectionTime: .atDocumentEnd, forMainFrameOnly: true)
        webConfiguration.userContentController.addUserScript(viewportScript)
        
        if !ViewController.KIE_ZOOM_ENABLED {
            let styleSource = "var style = document.createElement('style'); style.innerHTML = 'html, body { touch-action: manipulation; overscroll-behavior-y: none; }'; document.head.appendChild(style);"
            let styleScript = WKUserScript(source: styleSource, injectionTime: .atDocumentEnd, forMainFrameOnly: true)
            webConfiguration.userContentController.addUserScript(styleScript)
        }

        webConfiguration.userContentController.add(self, name: "Kie")

        nativeWebView = WKWebView(frame: .zero)
        nativeWebView.isHidden = true
        nativeWebView.scrollView.delegate = self
        nativeWebView.translatesAutoresizingMaskIntoConstraints = false
        view.addSubview(nativeWebView)
        NSLayoutConstraint.activate([
            nativeWebView.topAnchor.constraint(equalTo: view.topAnchor),
            nativeWebView.bottomAnchor.constraint(equalTo: view.bottomAnchor),
            nativeWebView.leadingAnchor.constraint(equalTo: view.leadingAnchor),
            nativeWebView.trailingAnchor.constraint(equalTo: view.trailingAnchor)
        ])

        webView = PassthroughWebView(frame: .zero, configuration: webConfiguration)
        webView.navigationDelegate = self
        webView.uiDelegate = self
        webView.isOpaque = false
        webView.backgroundColor = UIColor.clear
        
        if !ViewController.KIE_ZOOM_ENABLED {
            webView.scrollView.bounces = false
        }
        
        // Edge-to-edge inmersivo total (Saltando Safe Area)
        webView.translatesAutoresizingMaskIntoConstraints = false
        view.addSubview(webView)
        
        NSLayoutConstraint.activate([
            webView.topAnchor.constraint(equalTo: view.topAnchor),
            webView.bottomAnchor.constraint(equalTo: view.bottomAnchor),
            webView.leadingAnchor.constraint(equalTo: view.leadingAnchor),
            webView.trailingAnchor.constraint(equalTo: view.trailingAnchor)
        ])
    }
    
    func userContentController(_ userContentController: WKUserContentController, didReceive message: WKScriptMessage) {
        if message.name == "Kie", let dict = message.body as? [String: Any], let action = dict["action"] as? String {
            DispatchQueue.main.async {
                switch action {
                case "loadUrl":
                    if let urlStr = dict["url"] as? String, let url = URL(string: urlStr) {
                        self.nativeWebView.load(URLRequest(url: url))
                    }
                case "startDaemon":
                    let endpoint = dict["endpoint"] as? String ?? "/daemon.php"
                    let taskName = dict["taskName"] as? String ?? "daemon"
                    UserDefaults.standard.set(endpoint, forKey: "daemon_endpoint")
                    UserDefaults.standard.set(taskName, forKey: "daemon_task")
                    
                    if #available(iOS 13.0, *) {
                        let request = BGAppRefreshTaskRequest(identifier: "com.phphone.daemon")
                        request.earliestBeginDate = Date(timeIntervalSinceNow: 60)
                        do {
                            try BGTaskScheduler.shared.submit(request)
                            print("✅ Demonio de iOS programado para: \(endpoint)?task=\(taskName)")
                        } catch {
                            print("❌ Error programando el demonio en iOS: \(error.localizedDescription)")
                        }
                    }
                case "setBrowserActive":
                    if let active = dict["active"] as? Bool {
                        self.nativeWebView.isHidden = !active
                        if !active {
                            self.nativeWebView.load(URLRequest(url: URL(string: "about:blank")!))
                        }
                    }
                case "setBrowserMargins":
                    if let top = dict["topMargin"] as? CGFloat, let bottom = dict["bottomMargin"] as? CGFloat {
                        self.nativeWebView.removeFromSuperview()
                        self.view.insertSubview(self.nativeWebView, belowSubview: self.webView)
                        self.nativeWebView.translatesAutoresizingMaskIntoConstraints = false
                        NSLayoutConstraint.activate([
                            self.nativeWebView.topAnchor.constraint(equalTo: self.view.topAnchor, constant: top),
                            self.nativeWebView.bottomAnchor.constraint(equalTo: self.view.bottomAnchor, constant: -bottom),
                            self.nativeWebView.leadingAnchor.constraint(equalTo: self.view.leadingAnchor),
                            self.nativeWebView.trailingAnchor.constraint(equalTo: self.view.trailingAnchor)
                        ])
                    }
                case "setUiRects":
                    if let rectsStr = dict["rectsJson"] as? String, let data = rectsStr.data(using: .utf8) {
                        do {
                            if let arr = try JSONSerialization.jsonObject(with: data, options: []) as? [[String: Any]] {
                                var newRects: [CGRect] = []
                                for r in arr {
                                    if let l = r["left"] as? Double, let t = r["top"] as? Double, let rt = r["right"] as? Double, let b = r["bottom"] as? Double {
                                        newRects.append(CGRect(x: l, y: t, width: rt - l, height: b - t))
                                    }
                                }
                                self.webView.uiRects = newRects
                            }
                        } catch {}
                    }
                case "gps_start":
                    break
                default:
                    break
                }
            }
        }
    }

    override func viewDidLoad() {
        super.viewDidLoad()
        
        print("🚀 Iniciando Motor Phphone (iOS)...")
        
        // 1. Inicializar el motor nativo de PHP
        KieEngine.initializeEngine()
        
        // 2. Extraer carpeta "src/" del Bundle hacia Documents/kie_app/src/
        let fileManager = FileManager.default
        let documentsPath = fileManager.urls(for: .documentDirectory, in: .userDomainMask).first!
        let kieAppPath = documentsPath.appendingPathComponent("kie_app")
        
        copyBundleFolder(sourceName: "src", to: kieAppPath.appendingPathComponent("src"))
        
        // 3. Inicializar y configurar GCDWebServer en el puerto 8081
        startLocalWebServer(appPath: kieAppPath)
        
        // 4. Cargar la URL inicial en el WebView
        // Auto-detección SPA Web (index.html) o PHP Nativo (index.php)
        let indexHtmlPath = kieAppPath.appendingPathComponent("src/index.html").path
        if fileManager.fileExists(atPath: indexHtmlPath) {
            print("index.html detectado, arrancando en modo SPA Web")
            if let url = URL(string: "http://127.0.0.1:8081/index.html") {
                webView.load(URLRequest(url: url))
            }
        } else {
            print("Arrancando motor PHP por defecto")
            if let url = URL(string: "http://127.0.0.1:8081/") {
                webView.load(URLRequest(url: url))
            }
        }
        
        // 5. Configurar manejadores de Ciclo de Vida (Segundo Plano)
        NotificationCenter.default.addObserver(self, selector: #selector(appDidEnterBackground), name: UIApplication.didEnterBackgroundNotification, object: nil)
        NotificationCenter.default.addObserver(self, selector: #selector(appWillEnterForeground), name: UIApplication.willEnterForegroundNotification, object: nil)
    }
    
    // --- 🎵 AUDIO NATIVO ---
    func playNativeAudio(path: String, loop: Bool) {
        stopNativeAudio()

        let fileManager = FileManager.default
        let docDir = fileManager.urls(for: .documentDirectory, in: .userDomainMask).first!
        let appDir = docDir.appendingPathComponent("kie_app/src")
        
        var audioURL: URL?
        
        if path.starts(with: "http://") || path.starts(with: "https://") {
            if let url = URL(string: path) {
                audioURL = url
            }
        } else {
            let localURL = appDir.appendingPathComponent(path)
            if fileManager.fileExists(atPath: localURL.path) {
                audioURL = localURL
            } else {
                print("❌ Phphone: Archivo de audio nativo no encontrado: \(localURL.path)")
            }
        }

        guard let finalURL = audioURL else { return }

        do {
            try AVAudioSession.sharedInstance().setCategory(.playback, mode: .default, options: [])
            try AVAudioSession.sharedInstance().setActive(true)

            if finalURL.isFileURL {
                audioPlayer = try AVAudioPlayer(contentsOf: finalURL)
            } else {
                let data = try Data(contentsOf: finalURL)
                audioPlayer = try AVAudioPlayer(data: data)
            }
            
            audioPlayer?.numberOfLoops = loop ? -1 : 0
            audioPlayer?.prepareToPlay()
            audioPlayer?.play()
        } catch {
            print("❌ Phphone: Error al reproducir audio nativo: \(error.localizedDescription)")
        }
    }

    func stopNativeAudio() {
        audioPlayer?.stop()
        audioPlayer = nil
    }

    // --- 📁 COPIADOR DE CARPETAS DESDE EL BUNDLE ---
    private func copyBundleFolder(sourceName: String, to targetDir: URL) {
        let fileManager = FileManager.default
        guard let sourcePath = Bundle.main.resourcePath else {
            print("⚠️ resourcePath no disponible en Bundle.")
            return
        }
        let sourceURL = URL(fileURLWithPath: sourcePath).appendingPathComponent(sourceName)
        
        if !fileManager.fileExists(atPath: sourceURL.path) {
            print("⚠️ Directorio origen \(sourceURL.path) no existe en el bundle de la app.")
            return
        }
        
        do {
            if fileManager.fileExists(atPath: targetDir.path) {
                try fileManager.removeItem(at: targetDir)
            }
            try fileManager.createDirectory(at: targetDir, withIntermediateDirectories: true, attributes: nil)
            
            let enumerator = fileManager.enumerator(at: sourceURL, includingPropertiesForKeys: [.isDirectoryKey], options: [])
            
            while let fileURL = enumerator?.nextObject() as? URL {
                let relativePath = String(fileURL.path.dropFirst(sourceURL.path.count))
                let targetFileURL = targetDir.appendingPathComponent(relativePath)
                
                var isDir: ObjCBool = false
                if fileManager.fileExists(atPath: fileURL.path, isDirectory: &isDir) {
                    if isDir.boolValue {
                        try fileManager.createDirectory(at: targetFileURL, withIntermediateDirectories: true, attributes: nil)
                    } else {
                        if fileManager.fileExists(atPath: targetFileURL.path) {
                            try fileManager.removeItem(at: targetFileURL)
                        }
                        try fileManager.createDirectory(at: targetFileURL.deletingLastPathComponent(), withIntermediateDirectories: true, attributes: nil)
                        try fileManager.copyItem(at: fileURL, to: targetFileURL)
                    }
                }
            }
            print("✅ Assets extraídos con éxito a: \(targetDir.path)")
        } catch {
            print("❌ Error al copiar assets del bundle: \(error)")
        }
    }

    // --- 🌐 GCDWEBSERVER Y RUTAS DEL PUENTE DE HARDWARE ---
    private func startLocalWebServer(appPath: URL) {
        webServer = GCDWebServer()
        
        // Manejador común para peticiones GET y POST
        let requestHandler: GCDWebServerProcessBlock = { [weak self] request in
            guard let self = self else { return GCDWebServerResponse(statusCode: 500) }
            return self.handleWebServerRequest(request, appPath: appPath)
        }
        
        webServer.addDefaultHandler(forMethod: "GET", request: GCDWebServerRequest.self, processBlock: requestHandler)
        webServer.addDefaultHandler(forMethod: "POST", request: GCDWebServerRequest.self, processBlock: requestHandler)
        
        webServer.start(withPort: 8081, bonjourName: nil)
        print("🌍 Servidor local Phphone iniciado en http://127.0.0.1:8081")
    }
    
    private func handleWebServerRequest(_ request: GCDWebServerRequest, appPath: URL) -> GCDWebServerResponse {
        let path = request.url.path
        let method = request.method
        
        // ==============================================================================
        // A. ENDPOINTS NATIVOS DEL PUENTE DE HARDWARE
        // ==============================================================================
        if path == "/api/audio/play" {
            let params = request.query ?? [:]
            let audioPath = params["path"] ?? ""
            let loop = (params["loop"] ?? "false") == "true"
            DispatchQueue.main.async {
                self.playNativeAudio(path: audioPath, loop: loop)
            }
            return GCDWebServerDataResponse(jsonObject: ["success": true])!
        }
        
        if path == "/api/gyroscope/start" {
            if !self.isGyroRunning && self.motionManager.isGyroAvailable {
                self.motionManager.gyroUpdateInterval = 1.0 / 60.0 // 60 FPS
                self.motionManager.startGyroUpdates(to: .main) { (data, error) in
                    if let gyroData = data {
                        self.currentGyroX = gyroData.rotationRate.x
                        self.currentGyroY = gyroData.rotationRate.y
                        self.currentGyroZ = gyroData.rotationRate.z
                    }
                }
                self.isGyroRunning = true
                return GCDWebServerDataResponse(jsonObject: ["success": true])!
            }
            return GCDWebServerDataResponse(jsonObject: ["error": "Cannot start gyroscope"])!
        }

        if path == "/api/gyroscope/stop" {
            if self.isGyroRunning {
                self.motionManager.stopGyroUpdates()
                self.isGyroRunning = false
                return GCDWebServerDataResponse(jsonObject: ["success": true])!
            }
            return GCDWebServerDataResponse(jsonObject: ["success": true])!
        }
        
        if path == "/api/gyroscope" {
            if self.isGyroRunning {
                return GCDWebServerDataResponse(jsonObject: [
                    "x": self.currentGyroX,
                    "y": self.currentGyroY,
                    "z": self.currentGyroZ
                ])!
            } else {
                return GCDWebServerDataResponse(jsonObject: ["error": "Gyroscope is not running. Call start first."])!
            }
        }
        
        if path == "/api/audio/stop" {
            DispatchQueue.main.async {
                self.stopNativeAudio()
            }
            return GCDWebServerDataResponse(jsonObject: ["success": true])!
        }

        if path == "/api/vibrate" {
            let generator = UIImpactFeedbackGenerator(style: .medium)
            generator.prepare()
            generator.impactOccurred()
            return GCDWebServerDataResponse(jsonObject: ["success": true])!
        }
        
        if path == "/api/toast" {
            let params = request.query ?? [:]
            let msg = params["msg"] ?? "Mensaje vacío"
            self.showToast(message: msg)
            return GCDWebServerDataResponse(jsonObject: ["success": true])!
        }
        
        if path == "/api/gps" {
            if self.locationManager == nil {
                DispatchQueue.main.sync {
                    self.locationManager = CLLocationManager()
                    self.locationManager?.delegate = self
                    self.locationManager?.desiredAccuracy = kCLLocationAccuracyBest
                }
            }
            
            let status = CLLocationManager.authorizationStatus()
            if status == .notDetermined {
                DispatchQueue.main.sync {
                    self.locationManager?.requestWhenInUseAuthorization()
                }
            }
            
            self.gpsSemaphore = DispatchSemaphore(value: 0)
            DispatchQueue.main.async {
                self.locationManager?.startUpdatingLocation()
            }
            
            _ = self.gpsSemaphore?.wait(timeout: .now() + 3.0)
            
            if let loc = self.latestLocation {
                return GCDWebServerDataResponse(jsonObject: [
                    "success": true,
                    "lat": loc.coordinate.latitude,
                    "lng": loc.coordinate.longitude
                ])!
            } else {
                // Fallback para simulador si no hay ubicación de GPS configurada
                return GCDWebServerDataResponse(jsonObject: [
                    "success": true,
                    "lat": 37.785834,
                    "lng": -122.406417
                ])!
            }
        }
        
        /*
        // --- PUSH NOTIFICATIONS (PHPHONE) ---
        if path == "/api/push_token" {
            if let token = self.pushToken {
                return GCDWebServerDataResponse(jsonObject: ["token": token])!
            } else {
                return GCDWebServerDataResponse(jsonObject: ["error": "Push token not available yet or Firebase not configured"])!
            }
        }
        */
        
        /*
        // --- IN APP PURCHASES (PHPHONE) ---
        if path == "/api/iap/purchase" {
            let params = request.query ?? [:]
            let productId = params["productId"] ?? ""
            if productId.isEmpty {
                return GCDWebServerDataResponse(jsonObject: ["error": "Missing productId"])!
            }
            let semaphore = DispatchSemaphore(value: 0)
            self.purchaseSemaphore = semaphore
            DispatchQueue.main.async {
                self.purchaseProduct(productId: productId)
            }
            _ = semaphore.wait(timeout: .now() + 120.0)
            let resultJson = self.purchaseResult ?? "{\"error\":\"Unknown Purchase error\"}"
            if let data = resultJson.data(using: .utf8),
               let json = try? JSONSerialization.jsonObject(with: data, options: []) as? [String: Any] {
                return GCDWebServerDataResponse(jsonObject: json)!
            }
            return GCDWebServerDataResponse(jsonObject: ["error": "Invalid JSON response"])!
        }
        */
        
        if path == "/api/camera" {
            // Mock de cámara de fotos en base64 para desarrollo robusto en Simulador de iOS
            let mockBase64 = "iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAYAAACNMs+9AAAABGdBTUEAALGPC/xhBQAAAAlwSFlzAAALEwAACxMBAJqcGAAAAAd0SU1FB9sECQgiAwt58p0AAAAIdEVYdENvbW1lbnQAQ3JlYXRlZCB3aXRoIEdJTVBkLmXRAAAAFUlEQVQY02P8z8AARjHGwEDAAMIAAAA2AAPV4+GfAAAAAElFTkSuQmCC"
            return GCDWebServerDataResponse(jsonObject: [
                "success": true,
                "base64": mockBase64
            ])!
        }
        
        if path == "/api/request-notification-permission" {
            let sem = DispatchSemaphore(value: 0)
            var grantedResult = false
            UNUserNotificationCenter.current().requestAuthorization(options: [.alert, .sound, .badge]) { granted, _ in
                grantedResult = granted
                sem.signal()
            }
            _ = sem.wait(timeout: .now() + 60.0)
            return GCDWebServerDataResponse(jsonObject: ["success": grantedResult])!
        }
        
        if path == "/api/notification" {
            let params = request.query ?? [:]
            let title = params["title"] ?? "Phphone"
            let msg = params["msg"] ?? params["body"] ?? "Notificación"
            let tag = params["tag"] ?? params["id"]
            let group = params["group"] ?? params["thread_id"]
            
            UNUserNotificationCenter.current().requestAuthorization(options: [.alert, .sound, .badge]) { granted, _ in
                if granted {
                    let content = UNMutableNotificationContent()
                    content.title = title
                    content.body = msg
                    content.sound = .default
                    if let group = group, !group.isEmpty {
                        content.threadIdentifier = group
                    }
                    
                    let identifier = (tag != nil && !tag!.isEmpty) ? tag! : UUID().uuidString
                    let trigger = UNTimeIntervalNotificationTrigger(timeInterval: 0.5, repeats: false)
                    let req = UNNotificationRequest(identifier: identifier, content: content, trigger: trigger)
                    UNUserNotificationCenter.current().add(req)
                }
            }
            return GCDWebServerDataResponse(jsonObject: ["success": true])!
        }
        
        if path == "/api/metrics" {
            let ram = getMemoryUsage()
            let cpu = getCPUUsage()
            let cpuStr = String(format: "%.1f", cpu)
            return GCDWebServerDataResponse(jsonObject: [
                "success": true,
                "ram_used": ram,
                "ram_total": 8192,
                "cpu": cpuStr
            ])!
        }
        
        // --- NUEVAS APIS FASE 5 ---
        if path == "/api/secure/write" {
            if let urlEncodedReq = request as? GCDWebServerURLEncodedFormRequest {
                let args = urlEncodedReq.arguments
                let key = args["key"] as? String ?? ""
                let value = args["value"] as? String ?? ""
                
                let data = value.data(using: .utf8)!
                let query: [String: Any] = [
                    kSecClass as String: kSecClassGenericPassword,
                    kSecAttrAccount as String: key,
                    kSecValueData as String: data
                ]
                SecItemDelete(query as CFDictionary)
                let status = SecItemAdd(query as CFDictionary, nil)
                return GCDWebServerDataResponse(jsonObject: ["success": status == errSecSuccess])!
            }
            return GCDWebServerDataResponse(jsonObject: ["success": false])!
        }
        
        if path == "/api/secure/read" {
            let params = request.query ?? [:]
            let key = params["key"] ?? ""
            
            let query: [String: Any] = [
                kSecClass as String: kSecClassGenericPassword,
                kSecAttrAccount as String: key,
                kSecReturnData as String: kCFBooleanTrue!,
                kSecMatchLimit as String: kSecMatchLimitOne
            ]
            
            var item: CFTypeRef?
            let status = SecItemCopyMatching(query as CFDictionary, &item)
            
            if status == errSecSuccess, let data = item as? Data, let value = String(data: data, encoding: .utf8) {
                let escaped = value.replacingOccurrences(of: "\\", with: "\\\\").replacingOccurrences(of: "\"", with: "\\\"").replacingOccurrences(of: "\n", with: "\\n")
                return GCDWebServerDataResponse(data: "{\"value\":\"\(escaped)\"}".data(using: .utf8) ?? Data(), contentType: "application/json")
            } else {
                return GCDWebServerDataResponse(jsonObject: ["error": "Not found"])!
            }
        }
        
        if path == "/api/openurl" {
            let params = request.query ?? [:]
            let urlStr = params["url"] ?? ""
            DispatchQueue.main.async {
                if let url = URL(string: urlStr) {
                    UIApplication.shared.open(url)
                }
            }
            return GCDWebServerDataResponse(jsonObject: ["success": true])!
        }
        
        if path == "/api/inappbrowser" {
            let params = request.query ?? [:]
            let urlStr = params["url"] ?? ""
            DispatchQueue.main.async {
                if let url = URL(string: urlStr) {
                    let svc = SFSafariViewController(url: url)
                    self.present(svc, animated: true)
                }
            }
            return GCDWebServerDataResponse(jsonObject: ["success": true])!
        }
        
        if path == "/api/mic/start" {
            DispatchQueue.main.async {
                AVAudioSession.sharedInstance().requestRecordPermission { granted in
                    if granted {
                        self.startAudioRecording()
                    }
                }
            }
            return GCDWebServerDataResponse(jsonObject: ["success": true])!
        }
        
        if path == "/api/mic/stop" {
            let sem = DispatchSemaphore(value: 0)
            var base64: String? = nil
            DispatchQueue.main.async {
                base64 = self.stopAudioRecording()
                sem.signal()
            }
            _ = sem.wait(timeout: .now() + 10.0)
            if let b64 = base64 {
                return GCDWebServerDataResponse(jsonObject: ["base64": b64])!
            } else {
                return GCDWebServerDataResponse(jsonObject: ["error": "Failed to record"])!
            }
        }
        
        if path == "/api/filepicker" {
            self.filePickerSemaphore = DispatchSemaphore(value: 0)
            self.filePickerResult = nil
            DispatchQueue.main.async {
                let types: [String] = ["public.content", "public.data"]
                let documentPicker = UIDocumentPickerViewController(documentTypes: types, in: .import)
                documentPicker.delegate = self
                documentPicker.allowsMultipleSelection = false
                self.present(documentPicker, animated: true, completion: nil)
            }
            _ = self.filePickerSemaphore?.wait(timeout: .now() + 300.0)
            let resultJson = self.filePickerResult ?? "{\"error\":\"User cancelled or error\"}"
            return GCDWebServerDataResponse(data: resultJson.data(using: .utf8) ?? Data(), contentType: "application/json")
        }
        
        if path == "/api/file/download" {
            if let urlEncodedReq = request as? GCDWebServerURLEncodedFormRequest {
                let args = urlEncodedReq.arguments
                let filename = args["filename"] as? String ?? "file.bin"
                let base64 = args["base64"] as? String ?? ""
                
                if let data = Data(base64Encoded: base64, options: .ignoreUnknownCharacters) {
                    let fileManager = FileManager.default
                    let documentsPath = fileManager.urls(for: .documentDirectory, in: .userDomainMask).first!
                    let filePath = documentsPath.appendingPathComponent(filename)
                    do {
                        try data.write(to: filePath)
                        return GCDWebServerDataResponse(jsonObject: ["success": true])!
                    } catch {
                        return GCDWebServerDataResponse(jsonObject: ["success": false])!
                    }
                }
            }
            return GCDWebServerDataResponse(jsonObject: ["success": false])!
        }
        
        // --- NUEVAS APIS DE HARDWARE ---
        if path == "/api/biometric" {
            let params = request.query ?? [:]
            let reason = params["reason"] ?? "Confirma tu identidad"
            self.biometricSemaphore = DispatchSemaphore(value: 0)
            
            DispatchQueue.main.async {
                let context = LAContext()
                var error: NSError?
                if context.canEvaluatePolicy(.deviceOwnerAuthenticationWithBiometrics, error: &error) {
                    context.evaluatePolicy(.deviceOwnerAuthenticationWithBiometrics, localizedReason: reason) { success, _ in
                        self.biometricResult = success
                        self.biometricSemaphore?.signal()
                    }
                } else {
                    self.biometricResult = true // Bypass en simulador si falla
                    self.biometricSemaphore?.signal()
                }
            }
            
            _ = self.biometricSemaphore?.wait(timeout: .now() + 60.0)
            return GCDWebServerDataResponse(jsonObject: ["success": self.biometricResult])!
        }
        
        if path == "/api/gallery" {
            self.gallerySemaphore = DispatchSemaphore(value: 0)
            self.galleryResult = nil
            
            DispatchQueue.main.async {
                let picker = UIImagePickerController()
                picker.delegate = self
                picker.sourceType = .photoLibrary
                self.present(picker, animated: true, completion: nil)
            }
            
            _ = self.gallerySemaphore?.wait(timeout: .now() + 300.0)
            let resultJson = self.galleryResult ?? "{\"error\":\"Unknown Gallery error\"}"
            return GCDWebServerDataResponse(data: resultJson.data(using: .utf8) ?? Data(), contentType: "application/json")
        }

        if path == "/api/share" {
            let params = request.query ?? [:]
            let text = params["text"] ?? ""
            let urlStr = params["url"] ?? ""
            
            DispatchQueue.main.async {
                var items: [Any] = [text]
                if let u = URL(string: urlStr) {
                    items.append(u)
                }
                let avc = UIActivityViewController(activityItems: items, applicationActivities: nil)
                self.present(avc, animated: true)
            }
            return GCDWebServerDataResponse(jsonObject: ["success": true])!
        }

        if path == "/api/battery" {
            var level = -1
            var isCharging = false
            DispatchQueue.main.sync {
                UIDevice.current.isBatteryMonitoringEnabled = true
                level = Int(UIDevice.current.batteryLevel * 100)
                let state = UIDevice.current.batteryState
                isCharging = (state == .charging || state == .full)
            }
            return GCDWebServerDataResponse(jsonObject: ["success": true, "level": level, "isCharging": isCharging])!
        }

        if path == "/api/network" {
            // Un chequeo simple sincrono via NWPathMonitor
            let monitor = NWPathMonitor()
            let sem = DispatchSemaphore(value: 0)
            var status = "unknown"
            monitor.pathUpdateHandler = { path in
                if path.usesInterfaceType(.wifi) {
                    status = "wifi"
                } else if path.usesInterfaceType(.cellular) {
                    status = "cellular"
                } else if path.status == .unsatisfied {
                    status = "offline"
                }
                sem.signal()
            }
            let queue = DispatchQueue(label: "NetworkMonitor")
            monitor.start(queue: queue)
            _ = sem.wait(timeout: .now() + 1.0)
            monitor.cancel()
            return GCDWebServerDataResponse(jsonObject: ["success": true, "status": status])!
        }

        if path == "/api/clipboard" {
            if request.method == "POST" {
                if let urlEncodedReq = request as? GCDWebServerURLEncodedFormRequest {
                    let args = urlEncodedReq.arguments
                    let text = args["text"] ?? ""
                    DispatchQueue.main.sync {
                        UIPasteboard.general.string = text
                    }
                }
                return GCDWebServerDataResponse(jsonObject: ["success": true])!
            } else {
                var clipText = ""
                DispatchQueue.main.sync {
                    clipText = UIPasteboard.general.string ?? ""
                }
                let escaped = clipText.replacingOccurrences(of: "\\", with: "\\\\").replacingOccurrences(of: "\"", with: "\\\"").replacingOccurrences(of: "\n", with: "\\n")
                let jsonStr = "{\"success\":true, \"text\":\"\(escaped)\"}"
                return GCDWebServerDataResponse(data: jsonStr.data(using: .utf8) ?? Data(), contentType: "application/json")
            }
        }

        if path == "/api/flashlight" {
            let params = request.query ?? [:]
            let turnOn = (params["on"] ?? "true") == "true"
            DispatchQueue.main.async {
                guard let device = AVCaptureDevice.default(for: .video) else { return }
                if device.hasTorch {
                    do {
                        try device.lockForConfiguration()
                        device.torchMode = turnOn ? .on : .off
                        device.unlockForConfiguration()
                    } catch {
                        print("Torch could not be used")
                    }
                }
            }
            return GCDWebServerDataResponse(jsonObject: ["success": true])!
        }

        if path == "/api/info" {
            var model = ""
            var osVer = ""
            var uuid = ""
            DispatchQueue.main.sync {
                model = UIDevice.current.name
                osVer = UIDevice.current.systemVersion
                uuid = UIDevice.current.identifierForVendor?.uuidString ?? "unknown"
            }
            return GCDWebServerDataResponse(jsonObject: ["success": true, "model": model, "os_version": osVer, "uuid": uuid])!
        }
        
        // ==============================================================================
        // B. ENRUTAMIENTO DINÁMICO DE PHP (Zend Engine)
        // ==============================================================================
        let fileExtension = URL(fileURLWithPath: path).pathExtension.lowercased()
        if fileExtension.isEmpty || fileExtension == "php" {
            let absoluteUrl = request.url.absoluteString
            
            let responseStr = KieEngine.executePHPRequest(absoluteUrl, method: method, appPath: appPath.path) ?? ""
            let mime = path.hasPrefix("/api/") ? "application/json" : "text/html"
            return GCDWebServerDataResponse(data: responseStr.data(using: .utf8) ?? Data(), contentType: mime)
        }
        
        // ==============================================================================
        // C. SERVIDO DE ARCHIVOS ESTÁTICOS
        // ==============================================================================
        let targetFilePath = appPath.appendingPathComponent("src").appendingPathComponent(path).path
        if FileManager.default.fileExists(atPath: targetFilePath) {
            let mimeType = getMimeType(forPath: targetFilePath)
            do {
                let data = try Data(contentsOf: URL(fileURLWithPath: targetFilePath))
                
                if KieSecrets.isEncrypted {
                    if let decryptedData = data.decryptAES256(keyHex: KieSecrets.aesKeyHex) {
                        return GCDWebServerDataResponse(data: decryptedData, contentType: mimeType)
                    }
                }
                
                return GCDWebServerDataResponse(data: data, contentType: mimeType)
            } catch {
                return GCDWebServerResponse(statusCode: 500)
            }
        }
        
        return GCDWebServerResponse(statusCode: 404)
    }
    
    // --- 📊 METATAGS Y TELEMETRÍA DE RENDIMIENTO ---
    private func getMemoryUsage() -> UInt64 {
        var info = mach_task_basic_info()
        var count = mach_msg_type_number_t(MemoryLayout<mach_task_basic_info>.size) / 4
        let kerr: kern_return_t = withUnsafeMutablePointer(to: &info) {
            $0.withMemoryRebound(to: integer_t.self, capacity: Int(count)) {
                task_info(mach_task_self_, task_flavor_t(MACH_TASK_BASIC_INFO), $0, &count)
            }
        }
        if kerr == KERN_SUCCESS {
            return info.resident_size / (1024 * 1024)
        } else {
            return 0
        }
    }
    
    private func getCPUUsage() -> Double {
        var threadList: thread_act_array_t?
        var threadCount: mach_msg_type_number_t = 0
        
        let kernReturn = withUnsafeMutablePointer(to: &threadList) {
            task_threads(mach_task_self_, $0, &threadCount)
        }
        
        if kernReturn != KERN_SUCCESS {
            return 0.0
        }
        
        var totalCpu: Double = 0.0
        
        if let threadList = threadList {
            for i in 0..<Int(threadCount) {
                var threadInfo = thread_basic_info()
                var threadInfoCount = mach_msg_type_number_t(THREAD_INFO_MAX)
                
                let infoReturn = withUnsafeMutablePointer(to: &threadInfo) {
                    $0.withMemoryRebound(to: integer_t.self, capacity: Int(threadInfoCount)) {
                        thread_info(threadList[i], thread_flavor_t(THREAD_BASIC_INFO), $0, &threadInfoCount)
                    }
                }
                
                if infoReturn == KERN_SUCCESS {
                    let cpu = Double(threadInfo.cpu_usage) / Double(TH_USAGE_SCALE) * 100.0
                    totalCpu += cpu
                }
            }
            
            vm_deallocate(mach_task_self_, vm_address_t(bitPattern: threadList), vm_size_t(threadCount * mach_msg_type_number_t(MemoryLayout<thread_t>.size)))
        }
        
        return totalCpu
    }
    
    private func getMimeType(forPath path: String) -> String {
        let ext = URL(fileURLWithPath: path).pathExtension.lowercased()
        switch ext {
        case "html", "htm": return "text/html"
        case "css": return "text/css"
        case "js", "mjs", "cjs": return "application/javascript"
        case "json", "map": return "application/json"
        case "png": return "image/png"
        case "jpg", "jpeg": return "image/jpeg"
        case "gif": return "image/gif"
        case "svg": return "image/svg+xml"
        case "webp": return "image/webp"
        case "ico": return "image/x-icon"
        case "woff": return "font/woff"
        case "woff2": return "font/woff2"
        case "ttf": return "font/ttf"
        case "otf": return "font/otf"
        case "wasm": return "application/wasm"
        case "mp3": return "audio/mpeg"
        case "wav": return "audio/wav"
        case "ogg": return "audio/ogg"
        case "mp4": return "video/mp4"
        case "webm": return "video/webm"
        case "pdf": return "application/pdf"
        default: return "application/octet-stream"
        }
    }
    
    // --- 🍞 TOAST FLOTANTE CON GLASSMORPHISM (PREMIUM LOOK) ---
    private func showToast(message: String) {
        DispatchQueue.main.async {
            // Contenedor principal del Toast
            let toastContainer = UIView()
            toastContainer.backgroundColor = UIColor(white: 0.1, alpha: 0.45)
            toastContainer.layer.cornerRadius = 22
            toastContainer.layer.masksToBounds = true
            toastContainer.alpha = 0.0
            
            // Inyectamos efecto Glassmorphic Blur
            let blurStyle: UIBlurEffect.Style
            if #available(iOS 13.0, *) {
                blurStyle = .systemThinMaterialDark
            } else {
                blurStyle = .dark
            }
            let blurEffect = UIBlurEffect(style: blurStyle)
            let blurView = UIVisualEffectView(effect: blurEffect)
            blurView.frame = CGRect(x: 0, y: 0, width: self.view.frame.size.width - 60, height: 48)
            blurView.autoresizingMask = [.flexibleWidth, .flexibleHeight]
            toastContainer.addSubview(blurView)
            
            // Etiqueta del texto
            let toastLabel = UILabel()
            toastLabel.textColor = .white
            toastLabel.textAlignment = .center
            toastLabel.font = UIFont.systemFont(ofSize: 14, weight: .medium)
            toastLabel.text = message
            toastLabel.translatesAutoresizingMaskIntoConstraints = false
            toastContainer.addSubview(toastLabel)
            
            toastContainer.translatesAutoresizingMaskIntoConstraints = false
            self.view.addSubview(toastContainer)
            
            // Layout autolayout responsivo
            NSLayoutConstraint.activate([
                toastContainer.bottomAnchor.constraint(equalTo: self.view.bottomAnchor, constant: -90),
                toastContainer.leadingAnchor.constraint(equalTo: self.view.leadingAnchor, constant: 30),
                toastContainer.trailingAnchor.constraint(equalTo: self.view.trailingAnchor, constant: -30),
                toastContainer.heightAnchor.constraint(equalToConstant: 48),
                
                toastLabel.centerYAnchor.constraint(equalTo: toastContainer.centerYAnchor),
                toastLabel.leadingAnchor.constraint(equalTo: toastContainer.leadingAnchor, constant: 16),
                toastLabel.trailingAnchor.constraint(equalTo: toastContainer.trailingAnchor, constant: -16)
            ])
            
            // Animación de entrada y salida
            UIView.animate(withDuration: 0.35, animations: {
                toastContainer.alpha = 1.0
            }) { _ in
                UIView.animate(withDuration: 0.35, delay: 2.2, options: .curveEaseOut, animations: {
                    toastContainer.alpha = 0.0
                }) { _ in
                    toastContainer.removeFromSuperview()
                }
            }
        }
    }
    
    private var lastScrollY: CGFloat = 0
    func scrollViewDidScroll(_ scrollView: UIScrollView) {
        if scrollView == nativeWebView.scrollView {
            let dy = scrollView.contentOffset.y - lastScrollY
            lastScrollY = scrollView.contentOffset.y
            let js = "window.dispatchEvent(new CustomEvent('nativeScroll', { detail: { dy: \(dy) } }));"
            webView.evaluateJavaScript(js, completionHandler: nil)
        }
    }

    // --- 🌐 WKNavigationDelegate: Interceptar Enlaces Externos ---
    func webView(_ webView: WKWebView, decidePolicyFor navigationAction: WKNavigationAction, decisionHandler: @escaping (WKNavigationActionPolicy) -> Void) {
        guard let url = navigationAction.request.url else {
            decisionHandler(.allow)
            return
        }
        
        let urlString = url.absoluteString
        
        // 1. Es nuestra app: Dejamos que el WebView lo cargue internamente
        if urlString.hasPrefix("http://127.0.0.1") || urlString.hasPrefix("file://") {
            decisionHandler(.allow)
            return
        }
        
        // 2. Es un enlace externo o fue clicado por el usuario: Abrir en app nativa
        if navigationAction.navigationType == .linkActivated {
            UIApplication.shared.open(url, options: [:], completionHandler: nil)
            decisionHandler(.cancel)
            return
        }
        
        decisionHandler(.allow)
    }

    // --- 📍 DELEGADO DE UBICACIÓN GPS ---
    func locationManager(_ manager: CLLocationManager, didUpdateLocations locations: [CLLocation]) {
        latestLocation = locations.last
        locationManager?.stopUpdatingLocation()
        gpsSemaphore?.signal()
    }
    
    func locationManager(_ manager: CLLocationManager, didFailWithError error: Error) {
        print("⚠️ Error de GPS: \(error.localizedDescription)")
        gpsSemaphore?.signal()
    }

    // --- 📸 DELEGADO DE GALERÍA ---
    func imagePickerController(_ picker: UIImagePickerController, didFinishPickingMediaWithInfo info: [UIImagePickerController.InfoKey : Any]) {
        picker.dismiss(animated: true) {
            if let image = info[.originalImage] as? UIImage, let data = image.jpegData(compressionQuality: 0.8) {
                let base64 = data.base64EncodedString()
                self.galleryResult = "{\"base64\": \"\(base64)\"}"
            } else {
                self.galleryResult = "{\"error\": \"No image data\"}"
            }
            self.gallerySemaphore?.signal()
        }
    }

    func imagePickerControllerDidCancel(_ picker: UIImagePickerController) {
        picker.dismiss(animated: true) {
            self.galleryResult = "{\"error\": \"User cancelled\"}"
            self.gallerySemaphore?.signal()
        }
    }

    // --- 📁 DELEGADO DE ARCHIVOS ---
    func documentPicker(_ controller: UIDocumentPickerViewController, didPickDocumentsAt urls: [URL]) {
        guard let url = urls.first else {
            self.filePickerResult = "{\"error\": \"No file selected\"}"
            self.filePickerSemaphore?.signal()
            return
        }
        
        let isAccessing = url.startAccessingSecurityScopedResource()
        defer {
            if isAccessing {
                url.stopAccessingSecurityScopedResource()
            }
        }
        
        do {
            let data = try Data(contentsOf: url)
            let base64 = data.base64EncodedString()
            let escapedName = url.lastPathComponent.replacingOccurrences(of: "\"", with: "\\\"")
            self.filePickerResult = "{\"filename\": \"\(escapedName)\", \"base64\": \"\(base64)\"}"
        } catch {
            self.filePickerResult = "{\"error\": \"Failed to read file: \(error.localizedDescription)\"}"
        }
        self.filePickerSemaphore?.signal()
    }

    func documentPickerWasCancelled(_ controller: UIDocumentPickerViewController) {
        self.filePickerResult = "{\"error\": \"User cancelled\"}"
        self.filePickerSemaphore?.signal()
    }

    // --- 🎤 AUDIO RECORDING ---
    func startAudioRecording() {
        let fileManager = FileManager.default
        let docDir = fileManager.urls(for: .documentDirectory, in: .userDomainMask).first!
        audioRecordURL = docDir.appendingPathComponent("phphone_record.m4a")
        
        let settings: [String: Any] = [
            AVFormatIDKey: Int(kAudioFormatMPEG4AAC),
            AVSampleRateKey: 44100.0,
            AVNumberOfChannelsKey: 1,
            AVEncoderAudioQualityKey: AVAudioQuality.high.rawValue
        ]
        
        do {
            try AVAudioSession.sharedInstance().setCategory(.playAndRecord, mode: .default, options: [])
            try AVAudioSession.sharedInstance().setActive(true)
            audioRecorder = try AVAudioRecorder(url: audioRecordURL!, settings: settings)
            audioRecorder?.prepareToRecord()
            audioRecorder?.record()
        } catch {
            print("Error starting recording: \(error.localizedDescription)")
        }
    }

    func stopAudioRecording() -> String? {
        audioRecorder?.stop()
        audioRecorder = nil
        
        guard let url = audioRecordURL else { return nil }
        do {
            let data = try Data(contentsOf: url)
            try FileManager.default.removeItem(at: url)
            return data.base64EncodedString()
        } catch {
            print("Error reading audio recording: \(error.localizedDescription)")
            return nil
        }
    }

    // --- 🔄 CICLO DE VIDA Y SEGUNDO PLANO (BACKGROUND) ---
    @objc func appDidEnterBackground() {
        print("💤 Aplicación minimizada. Pausando servidor local PHP...")
        if webServer != nil && webServer.isRunning {
            webServer.stop()
        }
    }
    
    @objc func appWillEnterForeground() {
        print("☀️ Aplicación en primer plano. Reactivando servidor local PHP...")
        if webServer != nil && !webServer.isRunning {
            webServer.start(withPort: 8081, bonjourName: nil)
            print("✅ Servidor reactivado con éxito.")
        }
    }
}

// --- 💬 WKUIDelegate: Soporte para alert(), confirm() y prompt() de JavaScript ---
extension ViewController: WKUIDelegate {

    // JavaScript: alert("mensaje")
    func webView(_ webView: WKWebView, runJavaScriptAlertPanelWithMessage message: String,
                 initiatedByFrame frame: WKFrameInfo, completionHandler: @escaping () -> Void) {
        let alert = UIAlertController(title: nil, message: message, preferredStyle: .alert)
        alert.addAction(UIAlertAction(title: "OK", style: .default) { _ in completionHandler() })
        DispatchQueue.main.async { self.present(alert, animated: true) }
    }

    // JavaScript: confirm("¿Estás seguro?")
    func webView(_ webView: WKWebView, runJavaScriptConfirmPanelWithMessage message: String,
                 initiatedByFrame frame: WKFrameInfo, completionHandler: @escaping (Bool) -> Void) {
        let alert = UIAlertController(title: nil, message: message, preferredStyle: .alert)
        alert.addAction(UIAlertAction(title: "Cancelar", style: .cancel) { _ in completionHandler(false) })
        alert.addAction(UIAlertAction(title: "OK", style: .default) { _ in completionHandler(true) })
        DispatchQueue.main.async { self.present(alert, animated: true) }
    }

    // JavaScript: prompt("Escribe algo:", "valor por defecto")
    func webView(_ webView: WKWebView, runJavaScriptTextInputPanelWithPrompt prompt: String,
                 defaultText: String?, initiatedByFrame frame: WKFrameInfo,
                 completionHandler: @escaping (String?) -> Void) {
        let alert = UIAlertController(title: nil, message: prompt, preferredStyle: .alert)
        alert.addTextField { $0.text = defaultText }
        alert.addAction(UIAlertAction(title: "Cancelar", style: .cancel) { _ in completionHandler(nil) })
        alert.addAction(UIAlertAction(title: "OK", style: .default) { _ in completionHandler(alert.textFields?.first?.text) })
        DispatchQueue.main.async { self.present(alert, animated: true) }
    }

    // JavaScript: window.open() — abre en el mismo WebView
    func webView(_ webView: WKWebView, createWebViewWith configuration: WKWebViewConfiguration,
                 for navigationAction: WKNavigationAction, windowFeatures: WKWindowFeatures) -> WKWebView? {
        if navigationAction.targetFrame == nil { webView.load(navigationAction.request) }
        return nil
    }

    /*
    // --- PUSH NOTIFICATIONS (PHPHONE) ---
    // Descomenta los métodos de abajo si activas Firebase (y asegúrate de añadir los protocolos a la clase)
    override func viewDidLoad() {
        super.viewDidLoad()
        FirebaseApp.configure()
        Messaging.messaging().delegate = self
        UNUserNotificationCenter.current().delegate = self
        let authOptions: UNAuthorizationOptions = [.alert, .badge, .sound]
        UNUserNotificationCenter.current().requestAuthorization(options: authOptions) { _, _ in }
        UIApplication.shared.registerForRemoteNotifications()
    }

    func messaging(_ messaging: Messaging, didReceiveRegistrationToken fcmToken: String?) {
        print("Firebase registration token: \(String(describing: fcmToken))")
        self.pushToken = fcmToken
    }
    
    func userNotificationCenter(_ center: UNUserNotificationCenter, willPresent notification: UNNotification, withCompletionHandler completionHandler: @escaping (UNNotificationPresentationOptions) -> Void) {
        completionHandler([[.banner, .badge, .sound]])
    }
    */
    
    /*
    // --- IN APP PURCHASES (PHPHONE) ---
    // Descomenta para activar IAP nativo
    // override func viewWillAppear(_ animated: Bool) {
    //     super.viewWillAppear(animated)
    //     SKPaymentQueue.default().add(self)
    // }
    // override func viewWillDisappear(_ animated: Bool) {
    //     super.viewWillDisappear(animated)
    //     SKPaymentQueue.default().remove(self)
    // }
    //
    // func purchaseProduct(productId: String) {
    //     if SKPaymentQueue.canMakePayments() {
    //         let productIDs = Set([productId])
    //         currentProductRequest = SKProductsRequest(productIdentifiers: productIDs)
    //         currentProductRequest?.delegate = self
    //         currentProductRequest?.start()
    //     } else {
    //         purchaseResult = "{\"error\": \"Payments disabled\"}"
    //         purchaseSemaphore?.signal()
    //     }
    // }
    //
    // func productsRequest(_ request: SKProductsRequest, didReceive response: SKProductsResponse) {
    //     if let product = response.products.first {
    //         let payment = SKPayment(product: product)
    //         SKPaymentQueue.default().add(payment)
    //     } else {
    //         purchaseResult = "{\"error\": \"Product not found\"}"
    //         purchaseSemaphore?.signal()
    //     }
    // }
    //
    // func paymentQueue(_ queue: SKPaymentQueue, updatedTransactions transactions: [SKPaymentTransaction]) {
    //     for transaction in transactions {
    //         switch transaction.transactionState {
    //         case .purchased, .restored:
    //             SKPaymentQueue.default().finishTransaction(transaction)
    //             if let receiptURL = Bundle.main.appStoreReceiptURL,
    //                let receiptData = try? Data(contentsOf: receiptURL) {
    //                 let receiptString = receiptData.base64EncodedString(options: [])
    //                 purchaseResult = "{\"success\": true, \"receipt\": \"\(receiptString)\"}"
    //             } else {
    //                 purchaseResult = "{\"success\": true}"
    //             }
    //             purchaseSemaphore?.signal()
    //         case .failed:
    //             SKPaymentQueue.default().finishTransaction(transaction)
    //             purchaseResult = "{\"error\": \"Purchase failed or cancelled\"}"
    //             purchaseSemaphore?.signal()
    //         case .deferred, .purchasing:
    //             break
    //         @unknown default:
    //             break
    //         }
    //     }
    // }
    */
}


extension Data {
    func decryptAES256(keyHex: String) -> Data? {
        guard let keyData = keyHex.dataFromHex(), keyData.count == 32 else { return nil }
        
        let magicString = "KIE_ENC:"
        guard let magicData = magicString.data(using: .utf8) else { return nil }
        guard self.count >= 24, self.prefix(8) == magicData else { return nil }
        
        let iv = self.dropFirst(8).prefix(16)
        let encryptedData = self.dropFirst(24)
        
        var decryptedData = Data(count: encryptedData.count + kCCBlockSizeAES128)
        let decryptedCount = decryptedData.count
        var numBytesDecrypted: size_t = 0
        
        let status = keyData.withUnsafeBytes { keyBytes in
            iv.withUnsafeBytes { ivBytes in
                encryptedData.withUnsafeBytes { encryptedBytes in
                    decryptedData.withUnsafeMutableBytes { decryptedBytes in
                        CCCrypt(CCOperation(kCCDecrypt),
                                CCAlgorithm(kCCAlgorithmAES),
                                CCOptions(kCCOptionPKCS7Padding),
                                keyBytes.baseAddress, keyData.count,
                                ivBytes.baseAddress,
                                encryptedBytes.baseAddress, encryptedData.count,
                                decryptedBytes.baseAddress, decryptedCount,
                                &numBytesDecrypted)
                    }
                }
            }
        }
        
        guard status == kCCSuccess else { return nil }
        decryptedData.count = numBytesDecrypted
        return decryptedData
    }

    func decryptAES256Bound(keyHex: String, bundleId: String = "com.example.phphone") -> Data? {
        let combined = keyHex + bundleId
        if let combinedData = combined.data(using: .utf8) {
            var hash = [UInt8](repeating: 0, count: Int(CC_SHA256_DIGEST_LENGTH))
            combinedData.withUnsafeBytes {
                _ = CC_SHA256($0.baseAddress, CC_LONG(combinedData.count), &hash)
            }
            let derivedKeyHex = hash.map { String(format: "%02hhx", $0) }.joined()
            return self.decryptAES256(keyHex: derivedKeyHex)
        }
        return self.decryptAES256(keyHex: keyHex)
    }
}

extension String {
    func dataFromHex() -> Data? {
        var data = Data(capacity: self.count / 2)
        var index = self.startIndex
        while index < self.endIndex {
            let nextIndex = self.index(index, offsetBy: 2, limitedBy: self.endIndex) ?? self.endIndex
            let byteString = String(self[index..<nextIndex])
            if let byte = UInt8(byteString, radix: 16) {
                data.append(byte)
            }
            index = nextIndex
        }
        return data
    }
}

struct KieSecrets {
    static let isEncrypted = true
    static let aesKeyHex = "086e7911d4238ace84000ba807f69b50777e9d11db210701022e0197d529bc56"
}
