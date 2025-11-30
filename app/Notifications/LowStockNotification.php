<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification
{
    /**
     * @param array<int, string> $channels
     */
    public function __construct(private Product $product, private array $channels)
    {
    }

    public function via(object $notifiable): array
    {
        return $this->channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $product = $this->product;

        return (new MailMessage)
            ->subject('Alerta de inventario bajo: ' . $product->name)
            ->greeting('Hola equipo de Compras,')
            ->line('El producto ' . $product->name . ' (' . ($product->sku ?? 'sin SKU') . ') se encuentra por debajo del nivel mínimo definido.')
            ->line('Stock actual: ' . $product->stock)
            ->line('Nivel mínimo configurado: ' . $product->reorder_level)
            ->action('Revisar productos', route('products.index'))
            ->line('Este aviso se generó automáticamente cuando se detectó la reducción de inventario.');
    }

    public function toSlack(object $notifiable): SlackMessage
    {
        $product = $this->product;

        return (new SlackMessage)
            ->error()
            ->content(':rotating_light: *Alerta de inventario bajo*')
            ->attachment(function ($attachment) use ($product) {
                $attachment->title($product->name, route('products.index'))
                    ->fields([
                        'SKU' => $product->sku ?? 'No asignado',
                        'Stock actual' => (string) $product->stock,
                        'Nivel mínimo' => (string) $product->reorder_level,
                    ]);
            });
    }
}
