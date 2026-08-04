#include <Foundation/Foundation.h>
#import "kie_engine.h"
#include <mutex>

// ==============================================================================
// Phphone C++ / Objective-C++ Engine Bridge
// ==============================================================================
// Este archivo carga la librería estática de PHP (libphp.a) e inyecta los
// scripts PHP en la memoria del dispositivo iOS de forma persistente y segura.
// ==============================================================================

#ifdef __cplusplus
extern "C" {
#endif

#include "sapi/embed/php_embed.h"
#include "Zend/zend_alloc.h"

#ifdef __cplusplus
}
#endif

static std::mutex php_mutex;
static BOOL isEngineInitialized = NO;

@implementation KieEngine

+ (void)initializeEngine {
    std::lock_guard<std::mutex> lock(php_mutex);
    if (!isEngineInitialized) {
        NSLog(@"🚀 KieEngine: Inicializando Zend Engine...");
        int argc = 1;
        char *argv[2] = { (char*)"kie_engine", nullptr };
        php_embed_init(argc, argv);
        isEngineInitialized = YES;
        NSLog(@"🚀 KieEngine: Zend Engine listo y cargado en memoria.");
    }
}

+ (void)shutdownEngine {
    std::lock_guard<std::mutex> lock(php_mutex);
    if (isEngineInitialized) {
        NSLog(@"🛑 KieEngine: Apagando Zend Engine...");
        php_embed_shutdown();
        isEngineInitialized = NO;
        NSLog(@"🛑 KieEngine: Zend Engine apagado correctamente.");
    }
}

+ (NSString *)executePHPCode:(NSString *)phpCode {
    // Bloqueo de concurrencia: PHP NTS no soporta ejecuciones concurrentes en memoria
    std::lock_guard<std::mutex> lock(php_mutex);
    
    if (!isEngineInitialized) {
        NSLog(@"⚠️ KieEngine Error: Intento de ejecutar código sin inicializar el motor.");
        return @"Error KieEngine: El motor PHP no ha sido inicializado.";
    }
    
    NSString *finalOutput = @"";
    
    // Inyectamos ob_start() dinámicamente antes del código del usuario
    NSString *wrappedCode = [NSString stringWithFormat:@"ob_start(); %@ $kie_out = ob_get_clean(); echo $kie_out;", phpCode];
    
    @try {
        // Ejecutar el código en el motor Zend
        zend_eval_string((char *)[wrappedCode UTF8String], NULL, "Phphone_iOS_Runtime");
        finalOutput = @"[Ejecución PHP completada]";
    } @catch (NSException *exception) {
        finalOutput = [NSString stringWithFormat:@"Error Fatal PHP: %@", exception.reason];
        NSLog(@"❌ KieEngine Error: %@", finalOutput);
    }
    
    return finalOutput;
}

+ (NSString *)executePHPRequest:(NSString *)url method:(NSString *)method appPath:(NSString *)appPath {
    std::lock_guard<std::mutex> lock(php_mutex);
    
    if (!isEngineInitialized) {
        NSLog(@"⚠️ KieEngine Error: Intento de ejecutar solicitud sin inicializar el motor.");
        return @"{\"success\":false,\"error\":\"KieEngine no inicializado\"}";
    }
    
    NSString *responsePath = [appPath stringByAppendingPathComponent:@"kie_response.txt"];
    NSString *errorPath = [appPath stringByAppendingPathComponent:@"kie_error.txt"];
    
    // Borrar archivos previos
    [[NSFileManager defaultManager] removeItemAtPath:responsePath error:nil];
    [[NSFileManager defaultManager] removeItemAtPath:errorPath error:nil];
    
    // Configurar e inyectar variables superglobales a Zend
    // Convertir a path relativo para index.php o ruta correspondiente
    NSString *script = [NSString stringWithFormat:
        @"try {\n"
        "  $base_path = '%@';\n"
        "  chdir($base_path);\n"
        "  ini_set('display_errors', 0);\n"
        "  ini_set('log_errors', 1);\n"
        "  ini_set('error_log', $base_path . '/kie_error.txt');\n"
        "  $index_php = $base_path . '/src/index.php';\n"
        "  $index_html = $base_path . '/src/index.html';\n"
        "  $index_path = file_exists($index_php) ? $index_php : $index_html;\n"
        "  $request_uri = '%@';\n"
        "  $parsed_url = parse_url($request_uri);\n"
        "  $parsed_path = $parsed_url['path'] ?? '/';\n"
        "  $relative_path = $parsed_path;\n"
        "  $target_path = $base_path . '/src' . $relative_path;\n"
        "  if (empty($relative_path) || $relative_path === '/' || !file_exists($target_path) || is_dir($target_path)) { $target_path = $index_path; }\n"
        "  if (!file_exists($target_path)) { file_put_contents($base_path . '/kie_response.txt', json_encode(['success'=>false, 'error'=>'Falta ' . $target_path])); return; }\n"
        "  $_SERVER['REQUEST_URI'] = $request_uri;\n"
        "  $_SERVER['REQUEST_METHOD'] = '%@';\n"
        "  $_SERVER['SCRIPT_NAME'] = $relative_path;\n"
        "  parse_str($parsed_url['query'] ?? '', $_GET);\n"
        "  ob_start();\n"
        "  if (file_exists($base_path . '/src/Phphone/Device.php')) { require_once $base_path . '/src/Phphone/Device.php'; }\n"
        "  require $target_path;\n"
        "  file_put_contents($base_path . '/kie_response.txt', ob_get_clean());\n"
        "} catch (\\Throwable $e) {\n"
        "  file_put_contents('%@/kie_response.txt', json_encode(['success'=>false, 'error'=>'Excepcion PHP: ' . $e->getMessage()]));\n"
        "}\n",
        appPath, url, method, appPath];
    
    zend_eval_string((char *)[script UTF8String], NULL, "Kie_iOS_Bridge");
    
    NSError *err = nil;
    NSString *responseText = [NSString stringWithContentsOfFile:responsePath encoding:NSUTF8StringEncoding error:&err];
    if (responseText && responseText.length > 0) {
        return responseText;
    } else {
        NSString *errorLog = [NSString stringWithContentsOfFile:errorPath encoding:NSUTF8StringEncoding error:nil];
        if (errorLog && errorLog.length > 0) {
            // Escapar comillas para JSON
            NSString *escapedErr = [errorLog stringByReplacingOccurrencesOfString:@"\"" withString:@"\\\""];
            escapedErr = [escapedErr stringByReplacingOccurrencesOfString:@"\n" withString:@" "];
            return [NSString stringWithFormat:@"{\"success\":false,\"error\":\"Log PHP: %@\"}", escapedErr];
        }
        return @"{\"success\":false,\"error\":\"Zend Engine Bailout (Posible Stack Overflow o Error fatal sin atrapar)\"}";
    }
}

@end
