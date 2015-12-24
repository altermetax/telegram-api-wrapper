<?php

class TelegramBot
{
  protected $token;
  const OFFSET_FROM_FILE = "offset_from_file";
  public function __construct($token)
  {
    $this->token = $token;
  }

  protected function getBaseUrl()
  {
    $baseUrl = "https://api.telegram.org/bot".$this->token."/";
    return $baseUrl;
  }

  protected function runRequest($apiMethod, $args = [])
  {
    $url = $this->getBaseUrl().$apiMethod;
    foreach($args as $arg => $value)
    {
      if($value === null) unset($args[$arg]);
    }
    try {
      $request = new HTTPRequest($url, HttpRequest::METH_POST);
      $request->addPostFields($args);
      $request->send();
      $json = $request->getBody();
    } catch (Exception $e) {
      die("Exception while creating/sending HTTP POST request:\n".$e);
    }
    $object = json_decode($json);

    return $object;
  }

  protected function writeOffset($offset)
  {
    $file = fopen("offset", "w+");
    if(!$file) die("Can't open file 'offset' in w+ mode.");
    $write = fwrite($file, $offset);
    if(!$write) die("Can't write on file 'offset' in w+ mode.");
    $close = fclose($file);
    if(!$close) die("Can't close file 'offset' in w+ mode");

    return true;
  }

  protected function readOffset()
  {
    $file = fopen("offset", "r");
    if(!$file) die("Can't open file 'offset' in r mode");
    $offset = fread("offset", filesize("offset"));
    if($offset === false) die("Can't read from file 'offset' in r mode");
    $close = fclose($file);
    if(!$close) die("Can't close file 'offset' in r mode");

    return $offset;
  }

  public function getMessageType($message)
  {
    if(isset($message->text)) return "text";
    if(isset($message->photo)) return "photo";
    if(isset($message->voice)) return "voice";
    if(isset($message->audio)) return "audio";
    if(isset($message->video)) return "video";
    if(isset($message->location)) return "location";

    return false;
  }

  public function getUpdates($offset = null, $limit = null, $timeout = null)
  {
    if($offset === self::OFFSET_FROM_FILE) $offset = readOffset() + 1;
    $updates = $this->runRequest("getUpdates", [
      "offset" => $offset,
      "limit" => $limit,
      "timeout" => $timeout
    ]);
    $this->writeOffset(end($updates));

    return $updates;
  }

  public function getMe()
  {
    $me = $this->runRequest("getMe");

    return $me;
  }

  public function sendMessage($chat_id, $text, $parse_mode = null, $disable_web_page_preview = null, $reply_to_message_id = null, $reply_markup = null)
  {
    $message = $this->runRequest("sendMessage", [
      "chat_id" => $chat_id,
      "text" => $text,
      "parse_mode" => $parse_mode,
      "disable_web_page_preview" => $disable_web_page_preview,
      "reply_to_message_id" => $reply_to_message_id,
      "reply_markup" => $reply_markup
    ]);

    return $message;
  }

  public function forwardMessage($chat_id, $from_chat_id, $message_id)
  {
    $message = $this->runRequest("forwardMessage", [
      "chat_id" => $chat_id,
      "from_chat_id" => $from_chat_id,
      "message_id" => $message_id
    ]);

    return $message;
  }

  public function sendPhoto($chat_id, $photo, $caption = null, $reply_to_message_id = null, $reply_markup = null)
  {
    $message = $this->runRequest("sendPhoto", [
      "chat_id" => $chat_id,
      "photo" => $photo,
      "caption" => $caption,
      "reply_to_message_id" => $reply_to_message_id,
      "reply_markup" => $reply_markup
    ]);

    return $message;
  }

  public function sendAudio($chat_id, $audio, $duration = null, $performer = null, $title = null, $reply_to_message_id = null, $reply_markup = null)
  {
    $message = $this->runRequest("sendAudio", [
      "chat_id" => $chat_id,
      "audio" => $audio,
      "duration" => $duration,
      "performer" => $performer,
      "title" => $title,
      "reply_to_message_id" => $reply_to_message_id,
      "reply_markup" => $reply_markup
    ]);

    return $message;
  }

  public function sendDocument($chat_id, $document, $reply_to_message_id = null, $reply_markup = null)
  {
    $message = $this->runRequest("sendDocument", [
      "chat_id" => $chat_id,
      "document" => $document,
      "reply_to_message_id" => $reply_to_message_id,
      "reply_markup" => $reply_markup
    ]);

    return $message;
  }

  public function sendSticker($chat_id, $sticker, $reply_to_message_id = null, $reply_markup = null)
  {
    $message = $this->runRequest("sendSticker", [
      "chat_id" => $chat_id,
      "sticker" => $sticker,
      "reply_to_message_id" => $reply_to_message_id,
      "reply_markup" => $reply_markup
    ]);

    return $message;
  }

  public function sendVideo($chat_id, $video, $duration = null, $caption = null, $reply_to_message_id = null, $reply_markup = null)
  {
    $message = $this->runRequest("sendVideo", [
      "chat_id" => $chat_id,
      "video" => $video,
      "duration" => $duration,
      "caption" => $caption,
      "reply_to_message_id" => $reply_to_message_id,
      "reply_markup" => $reply_markup
    ]);

    return $message;
  }

  public function sendVoice($chat_id, $voice, $duration = null, $reply_to_message_id = null, $reply_markup = null)
  {
    $message = $this->runRequest("sendVoice", [
      "chat_id" => $chat_id,
      "voice" => $voice,
      "duration" => $duration,
      "reply_to_message_id" => $reply_to_message_id,
      "reply_markup" => $reply_markup
    ]);

    return $message;
  }

  public function sendLocation($chat_id, $latitude, $longitude, $reply_to_message_id = null, $reply_markup = null)
  {
    $message = $this->runRequest("sendLocation", [
      "chat_id" => $chat_id,
      "latitude" => $latitude,
      "longitude" => $longitude,
      "reply_to_message_id" => $reply_to_message_id,
      "reply_markup" => $reply_markup
    ]);

    return $message;
  }

  public function sendChatAction($chat_id, $action)
  {
    $this->runRequest("sendChatAction", [
      "chat_id" => $chat_id,
      "action" => $action
    ]);

    return true;
  }

  public function getUserProfilePhotos($user_id, $offset = null, $limit = null)
  {
    $photos = $this->runRequest("getUserProfilePhotos", [
      "user_id" => $user_id,
      "offset" => $offset,
      "limit" => $limit
    ]);

    return $photos;
  }

  public function getFile($file_id)
  {
    $file = $this->runRequest("getFile", [
      "file_id" => $file_id
    ]);

    return $file;
  }

  public function downloadFile($file)
  {
    $download = file_get_contents("https://api.telegram.org/file/bot".$this->token."/".$file->file_path);
    file_put_contents("downloads/".$file->file_path, $download);

    return $download;
  }

  public function createKeyboard($keyboard)
  {
    if(!isset($keyboard->keyboard)) throw new Exception("The object sent to createKeyboard() isn't a keyboard!");
    return json_encode($keyboard);
  }
}

 ?>
