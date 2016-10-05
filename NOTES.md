

Comunque cosa molto importante


c'è la possibilità di inviare i request? Mi serve
$request = new Request();
$request -> headers -> add('something')
$response = $request -> send();
$body = $response -> getBody();
$body = $response -> getParsedXMLBody();
simplexml_load_string(file_get_contents($url));
Ora uso questo, vorrei evitare



Investigate:
	scheme
	fragment
