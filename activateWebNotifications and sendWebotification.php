function activateWebNotifications(){

echo "
<script>
window.onload = () => {
  // (A1) ASK FOR PERMISSION
  console.log('activateWebNotifications, status = '+Notification.permission);
  if (Notification.permission === 'default') {
    Notification.requestPermission().then(perm => {
      if (Notification.permission === 'granted') {
		  
        regWorker().catch(err => console.error(err));
      } else {
        alert('Veuillez autoriser les notifications. ');
      }
    });
  } 
 
  // (A2) GRANTED
  else if (Notification.permission === 'granted') {
    regWorker().catch(err => console.error(err));
  }

  // (A3) DENIED
  else { 
  alert('Veuillez autoriser les notifications. ');
  }
};

// (B) REGISTER SERVICE WORKER
async function regWorker () {
  // (B1) YOUR PUBLIC KEY - CHANGE TO YOUR OWN!
  const publicKey = 'BEXxsfQklxVSWcDRTgPRPQbsLujMzCkKKOQdgjfb6Q84PEHKm_RTq74KcX6TMFsL0eH2BLprB2F3k6AKTFrnD0Y';
 
 console.log(window.location.href);
 if ((window.location.href).includes('DEV')){
	var scope1 = '/DEV/';
 }
 else{
	var scope = '/';
 }

  // (B2) REGISTER SERVICE WORKER
  navigator.serviceWorker.register('4-sw.js', { scope: scope1 });

  // (B3) SUBSCRIBE TO PUSH SERVER
  navigator.serviceWorker.ready
  .then(reg => {
	  console.log('subscribe to notification server');
    reg.pushManager.subscribe({
      userVisibleOnly: true,
      applicationServerKey: publicKey
    }).then(
      // (B3-1) OK - TEST PUSH SUSBSCRIPTION TO SERVER 
      sub => {
        var data = new FormData();
        data.append('sub', JSON.stringify(sub));
		console.log('sub', sub);
        fetch('web_notif_server.php', { method: 'POST', body : data })
        .then(res => res.text())
        .then(txt => console.log(txt))
        .catch(err => console.error(err));
      },
 
      // (B3-2) ERROR!
      err => console.error(err)
    );
  });
}


</script>";


//sendWebNotification("Nouvelle analyse d'un projet de crowdfunding.","test de l'analyse blabla...","me1.png","clubfunding.png","https://objectif-renta.com/vote.php");


require "vendor/autoload.php";
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

function sendWebNotification($title, $message, $icon, $image,$onclic_url){

	$query1 = "select * from WebNotificationSubscription";
	$result1 = sql_query($query1);
	$i=0;
	while ($i < sizeof($result1)){
		
		$sub = Subscription::create(json_decode($result1[$i]['details'], true));
		// (C) NEW WEB PUSH OBJECT - CHANGE TO YOUR OWN!
		$push = new WebPush(["VAPID" => [
		  "subject" => "contact@objectif-renta.com",
		  "publicKey" => "<your public key>",
		  "privateKey" => "<your private key>"
		]]);

		// (D) SEND TEST PUSH NOTIFICATION
		
		$result = $push->sendOneNotification($sub, json_encode([
		  "title" => "$title",
		  "body" => "$message",
		  "icon" => "pics/$icon",
		  "image" => "pics/$image",
		  "badge" => getWebSite()."/pics/badge.png",
		  "onclic_url"=> "$onclic_url"
		]));
		
		
		$endpoint = $result->getRequest()->getUri()->__toString();
		Logs::addM($i." - sending to {$endpoint}");

		// (E) SHOW RESULT - OPTIONAL
		if ($result->isSuccess()) {
		   Logs::addM("Successfully sent {$endpoint}.");
		} else {
		   Logs::addM("Send failed {$endpoint}: {$result->getReason()}");
		   $result->getRequest();
		   $result->getResponse();
		   $result->isSubscriptionExpired();
		}
		

		$i=$i+1;
	}
}
