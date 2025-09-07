<?php
declare(strict_types=1);

require_once __DIR__ . '/ResponseInterface.php';

/**
 * Implémentation concrète d'une réponse HTTP
 */
class HttpResponse implements ResponseInterface
{
    private int $statusCode;
    private string $content;
    private array $headers;
    private ?string $redirectUrl;
    
    public function __construct(int $statusCode = 200, string $content = '', array $headers = [])
    {
        $this->statusCode = $statusCode;
        $this->content = $content;
        $this->headers = $headers;
        $this->redirectUrl = null;
    }
    
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
    
    public function getContent(): string
    {
        return $this->content;
    }
    
    public function getHeaders(): array
    {
        return $this->headers;
    }
    
    public function getHeader(string $name): ?string
    {
        $name = strtolower($name);
        return $this->headers[$name] ?? null;
    }
    
    public function setStatusCode(int $code): void
    {
        $this->statusCode = $code;
    }
    
    public function setContent(string $content): void
    {
        $this->content = $content;
    }
    
    public function addHeader(string $name, string $value): void
    {
        $this->headers[strtolower($name)] = $value;
    }
    
    public function isRedirect(): bool
    {
        return $this->statusCode >= 300 && $this->statusCode < 400;
    }
    
    public function getRedirectUrl(): ?string
    {
        return $this->redirectUrl;
    }
    
    /**
     * Créer une réponse de redirection
     */
    public static function redirect(string $url, int $statusCode = 302): self
    {
        $response = new self($statusCode);
        $response->redirectUrl = $url;
        $response->addHeader('Location', $url);
        return $response;
    }
    
    /**
     * Créer une réponse JSON
     */
    public static function json(array $data, int $statusCode = 200): self
    {
        $response = new self($statusCode, json_encode($data, JSON_UNESCAPED_UNICODE));
        $response->addHeader('Content-Type', 'application/json');
        return $response;
    }
    
    /**
     * Créer une réponse d'erreur
     */
    public static function error(string $message, int $statusCode = 500): self
    {
        $response = new self($statusCode, $message);
        $response->addHeader('Content-Type', 'text/plain');
        return $response;
    }
    
    /**
     * Créer une réponse HTML
     */
    public static function html(string $content, int $statusCode = 200): self
    {
        $response = new self($statusCode, $content);
        $response->addHeader('Content-Type', 'text/html; charset=utf-8');
        return $response;
    }
    
    /**
     * Envoyer la réponse au client
     */
    public function send(): void
    {
        // Envoyer le code de statut
        http_response_code($this->statusCode);
        
        // Envoyer les headers
        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }
        
        // Envoyer le contenu
        echo $this->content;
    }
}
