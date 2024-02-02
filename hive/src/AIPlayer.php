<?php

namespace Lucas\Hive;

use GuzzleHttp\Client;


class AIPlayer
{
    private $client;
    private $moveNumber;
    private $hive;

    public function __construct(int $moveNumber, Hive $hive)
    {
        $this->moveNumber = $moveNumber;
        $this->hive = $hive;
        $this->client = new Client(['base_uri' => 'http://ai:5000']);
    }

    private  function getMove(Board $board, array $hands)
    {
        $handArray = array_map(function (Hand $hand) {
            return $hand->getHand();
        }, $hands);

        $options = [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'move_number' => $this->moveNumber,
                'hand' => $handArray,
                'board' => $board->getBoard()
            ]
        ];

        $response = $this->client->request('POST', '', $options);
        return json_decode($response->getBody()->getContents());
    }

    public function move(): array
    {
        $board = $this->hive->getBoard();
        $hands = $this->hive->getHands();
        [$type, $first, $to] = $this->getMove($board, $hands);

        if($type == 'play') {
            $moveId = $this->hive->play($to, $first);
        } elseif($type == 'move') {
            $moveId = $this->hive->move($first, $to);
        } else {
            $moveId = $this->hive->pass();
        }
        return [$type, $moveId];
    }
}
