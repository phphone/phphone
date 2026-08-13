<?php
if (!class_exists("PhphoneDecryptWrapper")) {
    class PhphoneDecryptWrapper {
        public $context;
    private $stream;
    private $aesKey;

    public function stream_open($path, $mode, $options, &$opened_path) {
        $this->aesKey = hex2bin("086e7911d4238ace84000ba807f69b50777e9d11db210701022e0197d529bc56");
        stream_wrapper_restore("file");
        
        // Si es un modo de escritura o append, simplemente pasamos el stream nativo transparente
        if (strpbrk($mode, "waxc+") !== false && strpos($mode, "r") === false) {
            $this->stream = @fopen($path, $mode);
            stream_wrapper_unregister("file");
            stream_wrapper_register("file", "PhphoneDecryptWrapper");
            return $this->stream !== false;
        }
        
        $fp = @fopen($path, "rb");
        if (!$fp) {
            stream_wrapper_unregister("file");
            stream_wrapper_register("file", "PhphoneDecryptWrapper");
            return false;
        }
        
        $content = stream_get_contents($fp);
        fclose($fp);
        
        if (strpos($content, "KIE_ENC:") === 0) {
            $iv = substr($content, 8, 16);
            $encrypted = substr($content, 24);
            $decrypted = @openssl_decrypt($encrypted, "AES-256-CBC", $this->aesKey, 1, $iv);
            if ($decrypted === false) {
                // Si la desencriptación falla, lanzamos el error exacto para debugear
                $err = openssl_error_string();
                throw new \Exception("PhphoneDecryptWrapper CRASH en $path. Motivo: " . ($err ?: "Desconocido"));
            }
        } else {
            $decrypted = $content;
        }
        
        // 1. Intentar memoria RAM pura
        $this->stream = @fopen("php://memory", "r+b");
        
        // 2. Fallback: Escribir archivo fantasma y borrarlo (se mantiene en memoria el handle)
        if (!$this->stream) {
            $baseAppDir = dirname(dirname(__DIR__)); // apunta a files/kie_app
            $ghostFile = $baseAppDir . "/.ghost_" . md5(uniqid());
            $this->stream = @fopen($ghostFile, "w+b");
            if ($this->stream) {
                @unlink($ghostFile);
            } else {
                stream_wrapper_unregister("file");
                stream_wrapper_register("file", "PhphoneDecryptWrapper");
                return false; // Error crítico: Ni memoria ni disco disponibles
            }
        }
        
        fwrite($this->stream, $decrypted);
        rewind($this->stream);
        
        stream_wrapper_unregister("file");
        stream_wrapper_register("file", "PhphoneDecryptWrapper");
        return true;
    }

    public function stream_read($count) {
        return fread($this->stream, $count);
    }
    public function stream_write($data) {
        return fwrite($this->stream, $data);
    }
    public function stream_eof() {
        return feof($this->stream);
    }
    public function stream_stat() {
        return fstat($this->stream);
    }
    public function stream_set_option($option, $arg1, $arg2) {
        return false;
    }
    public function url_stat($path, $flags) {
        stream_wrapper_restore("file");
        $stat = @stat($path);
        stream_wrapper_unregister("file");
        stream_wrapper_register("file", "PhphoneDecryptWrapper");
        return $stat;
    }

    // --- MANEJO DE DIRECTORIOS Y SISTEMA DE ARCHIVOS (Inyectados) ---

    public function mkdir($path, $mode, $options) {
        stream_wrapper_restore("file");
        $result = @mkdir($path, $mode, ($options & STREAM_MKDIR_RECURSIVE) === STREAM_MKDIR_RECURSIVE);
        stream_wrapper_unregister("file");
        stream_wrapper_register("file", "PhphoneDecryptWrapper");
        return $result;
    }

    public function rmdir($path, $options) {
        stream_wrapper_restore("file");
        $result = @rmdir($path);
        stream_wrapper_unregister("file");
        stream_wrapper_register("file", "PhphoneDecryptWrapper");
        return $result;
    }

    public function rename($path_from, $path_to) {
        stream_wrapper_restore("file");
        $result = @rename($path_from, $path_to);
        stream_wrapper_unregister("file");
        stream_wrapper_register("file", "PhphoneDecryptWrapper");
        return $result;
    }

    public function unlink($path) {
        stream_wrapper_restore("file");
        $result = @unlink($path);
        stream_wrapper_unregister("file");
        stream_wrapper_register("file", "PhphoneDecryptWrapper");
        return $result;
    }

    private $dir_handle;

    public function dir_opendir($path, $options) {
        stream_wrapper_restore("file");
        $this->dir_handle = @opendir($path);
        stream_wrapper_unregister("file");
        stream_wrapper_register("file", "PhphoneDecryptWrapper");
        return $this->dir_handle !== false;
    }

    public function dir_readdir() {
        return @readdir($this->dir_handle);
    }

    public function dir_rewinddir() {
        if ($this->dir_handle) {
            @rewinddir($this->dir_handle);
            return true;
        }
        return false;
    }

    public function dir_closedir() {
        if ($this->dir_handle) {
            @closedir($this->dir_handle);
            $this->dir_handle = null;
            return true;
        }
        return false;
    }

    // --- MANEJO DE PUNTEROS Y CACHÉ (Requeridos por SQLite) ---

    public function stream_seek($offset, $whence = SEEK_SET) {
        if ($this->stream) {
            return fseek($this->stream, $offset, $whence) === 0;
        }
        return false;
    }

    public function stream_tell() {
        if ($this->stream) {
            return ftell($this->stream);
        }
        return false;
    }

    public function stream_flush() {
        if ($this->stream) {
            return fflush($this->stream);
        }
        return false;
    }
    }
    stream_wrapper_unregister("file");
    stream_wrapper_register("file", "PhphoneDecryptWrapper");
}
