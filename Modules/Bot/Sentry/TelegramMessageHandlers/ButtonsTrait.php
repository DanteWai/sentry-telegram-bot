<?php

namespace Modules\Bot\Sentry\TelegramMessageHandlers;

trait ButtonsTrait
{
    /**
     * @param array $projects
     * @param array $exclude
     * @return array
     */
    public function getButtonsForProjects(array $projects, array $exclude = []): array
    {
        $map = array_flip($exclude);

        return array_reduce($projects, function($sum, $item) use ($map) {
            if(!isset($map[$item['id']])){
                $sum[] = [
                    'text' => $item['title'],
                    'callback_data' => 'cb_key_for_' . $item['id']
                ];
            }

            return $sum;
        },[]);
    }
}