<?php
declare(strict_types=1);

require_once __DIR__ . '/RequestInterface.php';

/**
 * Implémentation concrète d'une requête HTTP
 */
class HttpRequest implements RequestInterface
{
    private string $method;
    private string $uri;
    private array $queryParams;
    private array $postParams;
    private array $headers;
    private string $clientIp;
    private ?string $userAgent;
    
    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->uri = $_SERVER['REQUEST_URI'] ?? '/';
        $this->queryParams = $_GET ?? [];
        $this->postParams = $_POST ?? [];
        $this->headers = $this->parseHeaders();
        $this->clientIp = $this->getRealClientIp();
        $this->userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    }
    
    public function getMethod(): string
    {
        return $this->method;
    }
    
    public function getUri(): string
    {
        return $this->uri;
    }
    
    public function getQueryParams(): array
    {
        return $this->queryParams;
    }
    
    public function getPostParams(): array
    {
        return $this->postParams;
    }
    
    public function getHeaders(): array
    {
        return $this->headers;
    }
    
    public function getHeader(string $name, string $default = null): ?string
    {
        $name = strtolower($name);
        return $this->headers[$name] ?? $default;
    }
    
    public function getClientIp(): string
    {
        return $this->clientIp;
    }
    
    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }
    
    public function isPost(): bool
    {
        return $this->method === 'POST';
    }
    
    public function isGet(): bool
    {
        return $this->method === 'GET';
    }
    
    public function getQueryParam(string $key, $default = null)
    {
        return $this->queryParams[$key] ?? $default;
    }
    
    public function getPostParam(string $key, $default = null)
    {
        return $this->postParams[$key] ?? $default;
    }
    
    /**
     * Parser les headers HTTP
     */
    private function parseHeaders(): array
    {
        $headers = [];
        
        if (function_exists('getallheaders')) {
            $rawHeaders = getallheaders();
            if ($rawHeaders) {
                foreach ($rawHeaders as $name => $value) {
                    $headers[strtolower($name)] = $value;
                }
            }
        } else {
            // Fallback pour les serveurs qui n'ont pas getallheaders()
            foreach ($_SERVER as $key => $value) {
                if (strpos($key, 'HTTP_') === 0) {
                    $name = str_replace('_', '-', substr($key, 5));
                    $headers[strtolower($name)] = $value;
                }
            }
        }
        
        return $headers;
    }
    
    /**
     * Obtenir la vraie IP du client (gérer les proxies)
     */
    private function getRealClientIp(): string
    {
        $ipKeys = [
            'HTTP_CF_CONNECTING_IP',     // Cloudflare
            'HTTP_CLIENT_IP',            // Proxy
            'HTTP_X_FORWARDED_FOR',      // Load balancer
            'HTTP_X_FORWARDED',          // Proxy
            'HTTP_X_CLUSTER_CLIENT_IP',  // Cluster
            'HTTP_FORWARDED_FOR',        // Proxy
            'HTTP_FORWARDED',            // Proxy
            'REMOTE_ADDR'                // Standard
        ];
        
        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                
                // Gérer les IPs multiples (première IP)
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                
                // Valider l'IP
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
}
