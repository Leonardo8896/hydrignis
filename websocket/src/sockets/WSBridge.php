<?php
namespace Hydrignis\Websocket\Sockets;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use React\EventLoop\Loop;

class WSBridge implements MessageComponentInterface
{
    private \SplObjectStorage $mobiles;
    private \SplObjectStorage $devices;
    /** @var array<int,int>  resourceId ⇒ timestamp do último pong/msg */
    private array $lastSeen = [];

    private int $interval = 10;   // segs entre pings
    private int $timeout  = 30;   // derruba se ficar > timeout sem resposta

    public function __construct()
    {
        $this->mobiles  = new \SplObjectStorage;
        $this->devices  = new \SplObjectStorage;

        // cron de heartbeat
        Loop::addPeriodicTimer($this->interval, function () {
            $now = time();
            foreach ([$this->mobiles, $this->devices] as $set) {
                foreach ($set as $conn) {
                    $id = $conn->resourceId;

                    // se nunca respondeu ou expirou → dropa
                    if (!isset($this->lastSeen[$id]) || $now - $this->lastSeen[$id] > $this->timeout) {
                        echo "⏰ Timeout $id – fechando\n";
                        $conn->close();  // dispara onClose/onError
                        continue;
                    }

                    // envia ping‑frame (Ratchet ≥0.4)
                    if (method_exists($conn, 'ping')) {
                        $conn->ping();
                    } else {
                        // fallback: manda texto; a ESP32 pode ignorar
                        $conn->send('{"type":"ping"}');
                    }
                }
            }
        });
    }

    /* ---------- Eventos Ratchet ---------- */

    public function onOpen(ConnectionInterface $conn)
    {
        parse_str($conn->httpRequest->getUri()->getQuery(), $p);
        $key  = $p['WS_KEY']  ?? '';
        $type = $p['TYPE']    ?? '';

        if ($key !== $_ENV['KEY']) {
            $conn->send('WS_KEY invalida');  
            $conn->close();  
            return;
        }

        $this->lastSeen[$conn->resourceId] = time();

        if ($type === 'device') {
            $this->devices->attach($conn);
            echo "New device {$conn->resourceId}\n";
        } elseif ($type === 'mobile') {
            $this->mobiles->attach($conn);
            echo "New mobile {$conn->resourceId}\n";
        } else {
            $conn->send('TYPE invalido');  
            $conn->close();  
            return;
        }

        $this->exportState();
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        // marca atividade
        $this->lastSeen[$from->resourceId] = time();

        // se for resposta do ping, ignore
        if ($msg === 'pong' || $msg === '{"type":"pong"}') {
            return;
        }

        if ($this->devices->contains($from)) {
            foreach ($this->mobiles as $m) $m->send($msg);
        } elseif ($this->mobiles->contains($from)) {
            foreach ($this->devices as $d) $d->send($msg);
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->detach($conn);
        echo "Close {$conn->resourceId}\n";
        $this->exportState();
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "Erro {$conn->resourceId}: {$e->getMessage()}\n";
        $conn->close();
        $this->detach($conn);     // garante remoção
        $this->exportState();
    }

    /* ---------- Helpers ---------- */

    private function detach(ConnectionInterface $conn): void
    {
        unset($this->lastSeen[$conn->resourceId]);
        $this->devices->detach($conn); // detach ignora se não contém
        $this->mobiles->detach($conn);
    }

    /** Exporta lista de IDs conectados (exemplo simples) */
    private function exportState(): void
    {
        $state = [
            'devices' => array_map(fn($c) => $c->resourceId, iterator_to_array($this->devices)),
            'mobiles' => array_map(fn($c) => $c->resourceId, iterator_to_array($this->mobiles)),
            'updated' => time(),
        ];
        file_put_contents(__DIR__.'/../logs/connected.json', json_encode($state));
    }
}
