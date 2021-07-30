<?php
include_once($_SERVER['DOCUMENT_ROOT']. '/elliptic-php/lib/EC.php');
include_once($_SERVER['DOCUMENT_ROOT']. '/php-keccak/src/Keccak.php');

use Elliptic\EC;
use kornrunner\Keccak;

class Streamr {
    private $key;
    private $session;

    const ENDPOINT_HOST = 'https://streamr.network/api/v1/';

    function __construct($privateKey, $streamId) {
        $ec = new EC('secp256k1');
        $key = $ec->keyFromPrivate($privateKey, 'hex');
        $pubkey = $key->getPublic(false, 'hex');
        echo $address = '0x' . substr(Keccak::hash(substr(hex2bin($pubkey), 1), 256), 24);
        echo "\n\n";

        $this->key = $key;
        $this->address = $address;
        $this->streamId = $streamId;
    }

    private function postRequest($path, $payload = '', $header = array()) {
        $curl = curl_init(self::ENDPOINT_HOST . $path);

        if ($payload) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
        }

        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array_merge(array('Content-Type: application/json'), $header));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            throw new Exception(curl_error($curl));
        }

        return json_decode($response, false);
    }

    private function geerateChallenge() {
        try{
            return $this->postRequest('login/challenge/' .$this->address);
        } catch(Exception $e) {
            print_r($e->getMessage());
        }
    }

    private function signMessage($message) {
        $message2hash = "\x19Ethereum Signed Message:\n" . strlen($message) . $message;
        $messageHash = Keccak::hash($message2hash, 256);
        $messageSignature = $this->key->sign($messageHash, ['canonical' => true]);

        $r = str_pad($messageSignature->r->toString(16), 64, '0', STR_PAD_LEFT);
        $s = str_pad($messageSignature->s->toString(16), 64, '0', STR_PAD_LEFT);
        $v = dechex($messageSignature->recoveryParam + 27);

        return '0x' . $r . $s . $v;
    }

    private function getSession() {
        $session = $this->session;

        if ($session) {
            return $session;
        }

        $challenge = $this->geerateChallenge();
        $signature = $this->signMessage($challenge->challenge);

        try{
            $session = $this->postRequest('login/response', array(
                'challenge' => array(
                    'id' => $challenge->id,
                    'challenge' => $challenge->challenge
                ),
                'signature' => $signature,
                'address' => $this->address
            ));

            $this->session = $session;

            return $session;
        } catch(Exception $e) {
            print_r($e->getMessage());
        }
    }

    public function getSessionToken() {
        $session = $this->session;

        if ($session) {
            return $session->token;
        }

        return $this->getSession()->token;
    }

    public function getSessionLifetime() {
        $session = $this->session;

        if ($session) {
            return $session->expires;
        }

        return $this->getSession()->expires;
    }

    public function publishData($payload) {
        try{
            return $this->postRequest(
                'streams/' .urlencode($this->streamId). '/data',
                $payload,
                array('Authorization: Bearer ' .$this->getSessionToken())
            );
        } catch(Exception $e) {
            print_r($e->getMessage());
        }
    }
}
