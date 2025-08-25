<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>WhatsApp UI Mock</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: sans-serif;
      background-color: #ece5dd;
    }

    .whatsapp-container {
      max-width: 400px;
      height: 700px;
      margin: 20px auto;
      border: 1px solid #ccc;
      border-radius: 10px;
      overflow: hidden;
      background-color: #fff;
      display: flex;
      flex-direction: column;
    }

    .header {
      background-color: #075e54;
      color: white;
      padding: 15px;
      font-size: 20px;
    }

    .chat-area {
      flex: 1;
      padding: 10px;
      overflow-y: auto;
      background: #e5ddd5;
      display: flex;
      flex-direction: column;
    }

    .message {
      max-width: 70%;
      padding: 10px;
      margin: 5px 0;
      border-radius: 8px;
      line-height: 1.4;
    }

    .message.sent {
      background-color: #dcf8c6;
      align-self: flex-end;
    }

    .message.received {
      background-color: white;
      align-self: flex-start;
    }

    .input-area {
      display: flex;
      padding: 10px;
      background-color: #f0f0f0;
    }

    .input-area input {
      flex: 1;
      padding: 8px;
      border-radius: 20px;
      border: 1px solid #ccc;
      outline: none;
    }

    .input-area button {
      background-color: #075e54;
      color: white;
      border: none;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      margin-left: 10px;
      cursor: pointer;
    }
  </style>
</head>
<body>

<div class="whatsapp-container">
  <div class="header">WhatsApp</div>

  <div class="chat-area">
    <!-- <div class="message received">Hey! How are you?</div> -->
    <div class="message sent">Some text will display here. Some text will display here. Some text will display here. Some text will display here. </div>
    <!-- <div class="message received">All well here 😄</div> -->
  </div>
</div>

</body>
</html>