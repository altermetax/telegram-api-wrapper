# telegram-api-wrapper
A simple PHP wrapper for the HTTP Telegram bot API located [here](https://core.telegram.org/bots/api).

# Using it
The method names are identical to those [here](https://core.telegram.org/bots/api), and their arguments too.

Here are some basic examples:

```
$bot = new TelegramBot($token); // $token is a string given by Bot Father when creating your bot

$updates = $bot->getUpdates(); // Will get updates for your bot as a stdClass object.

foreach($updates as $update) { // Do the following for each new message:
  echo $update->message->text . "\n"; // Output the text of the message, you may add <br> if the application will be used from a web browser
  $bot->sendMessage($update->message->chat->id, "Message received."); // Send a confirmation message back to the user / group
}

$updates = $bot->getUpdates(TelegramBot::OFFSET_FROM_FILE); // This will automate the offset argument: the wrapper will request for the latest message for you, by reading it from a file named "offset" in the same folder as the TelegramBot.php file.

// Same thing works for photos (sendPhoto), videos (sendVideo), voices (sendVoice), legacy audios (sendAudio) and so on.

// Here I will explain how to get sent files:

$updates = $bot->getUpdates(TelegramBot::OFFSET_FROM_FILE); // Let's get new updates
foreach($updates as $update) { // Iterate updates
  if($bot->getMessageType($update->message) === "document") { // getMessageType() may return text photo voice audio video location sticker document and so on
    $file = getFile($update->message->document->file_id); // Gain a File object from file id
    $fileContent = $bot->downloadFile($file, "downloads"); // This line will download the file in downloads/<file_path> where file_path is the path where the file is located at telegram servers.
    // Then, you may use fileContent as you want: it obviously contains the file content (Warning: this is true even if it isn't a simple text file, such as an image, an executable etc.)
  }
}
```
