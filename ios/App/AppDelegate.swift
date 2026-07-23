import UIKit
import WebKit

@UIApplicationMain
class AppDelegate: UIResponder, UIApplicationDelegate {

    var window: UIWindow?
    private var isReloading = false

    func application(_ application: UIApplication, didFinishLaunchingWithOptions launchOptions: [UIApplication.LaunchOptionsKey: Any]?) -> Bool {
        // PHPHONE_INJECT:AUDIO_SESSION
        window = UIWindow(frame: UIScreen.main.bounds)
        window?.rootViewController = ViewController()
        window?.makeKeyAndVisible()
        return true
    }

    // Interceptar la URL scheme "phphone://reload" disparada por el CLI
    func application(_ app: UIApplication, open url: URL, options: [UIApplication.OpenURLOptionsKey : Any] = [:]) -> Bool {
        if url.scheme == "phphone" && url.host == "reload" {
            if isReloading {
                print("⏳ Ignorando Hot Reload: Ya hay una recarga en curso (Previniendo Bailout)")
                return true
            }
            
            isReloading = true
            print("🔥 Hot Reload Triggered!")
            
            if let vc = window?.rootViewController as? ViewController, let webView = vc.webView {
                // Limpiar caché explícitamente antes de recargar
                let dataStore = WKWebsiteDataStore.default()
                dataStore.fetchDataRecords(ofTypes: WKWebsiteDataStore.allWebsiteDataTypes()) { records in
                    dataStore.removeData(ofTypes: WKWebsiteDataStore.allWebsiteDataTypes(), for: records) {
                        DispatchQueue.main.async {
                            webView.reload()
                            
                            // Liberar el candado después de un tiempo prudencial
                            DispatchQueue.main.asyncAfter(deadline: .now() + 1.0) {
                                self.isReloading = false
                                print("✅ Hot Reload completado, candado liberado")
                            }
                        }
                    }
                }
            } else {
                isReloading = false
            }
            return true
        }
        return false
    }
}
