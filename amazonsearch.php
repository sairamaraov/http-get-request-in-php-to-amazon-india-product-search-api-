<?php
$q= "your search query goes here";

// Your AWS Access Key ID, as taken from the AWS Your Account page
$aws_access_key_id = "please use valid aws key here";
// Your AWS Secret Key corresponding to the above ID, as taken from the AWS Your Account page
$aws_secret_key = "your aws secret key";
// The region you are interested in
$endpoint = "webservices.amazon.in";//for india
$uri = "/onca/xml";
$params = array(
    "Service" => "AWSECommerceService",
    "Operation" => "ItemSearch",//for item search using query
    "AWSAccessKeyId" => "your AWS access key id",
    "AssociateTag" => "your associate tag",
    "SearchIndex" => "All",
    "ResponseGroup" => "Images,ItemAttributes,Offers",
    "Version" => "2015-10-01",
    "Keywords" => $q
);
// Set current timestamp if not set
if (!isset($params["Timestamp"])) {
    $params["Timestamp"] = gmdate('Y-m-d\TH:i:s\Z');
}
// Sort the parameters by key
ksort($params);
$pairs = array();
foreach ($params as $key => $value) {
    array_push($pairs, rawurlencode($key)."=".rawurlencode($value));
}
// Generate the canonical query
$canonical_query_string = join("&", $pairs);
// Generate the string to be signed
$string_to_sign = "GET\n".$endpoint."\n".$uri."\n".$canonical_query_string;
// Generate the signature required by the Product Advertising API
$signature = base64_encode(hash_hmac("sha256", $string_to_sign, $aws_secret_key, true));
// Generate the signed URL
$request_url = 'http://'.$endpoint.$uri.'?'.$canonical_query_string.'&Signature='.rawurlencode($signature);
//echo "Signed URL: \"".$request_url."\"";
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL =>$request_url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "cache-control: no-cache"
  ),
));
$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);
if ($err) {
  echo "cURL Error #:" . $err;
} else {
$xml = simplexml_load_string($response, "SimpleXMLElement", LIBXML_NOCDATA);
$json = json_encode($xml);
$array = json_decode($json,TRUE);

}

$out=" ";
$i=0;
for($k=0;$k<5;$k++) //this loop is repeated only 5 times to fetch 5 results.you can increase the number.
{
$out.='<tr><td> <a target="_blank" href="'.$array[Items][Item][$i][DetailPageURL].'"> <img style="width:100%;max-width:200px" src="'.$array[Items][Item][$i][MediumImage][URL].'" alt="image" /> </a>  <br/> '.$array[Items][Item][$i][ItemAttributes][Title].'<br/><b> Price:</b>'.$array[Items][Item][$i][OfferSummary][LowestNewPrice][FormattedPrice].'</td></tr>';
$i++;
}
echo "<table ><tr><th>amazon</th></tr>".$out."</table>";
?>
