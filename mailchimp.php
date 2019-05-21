<?php


$email = 'claudevernetmichel22@gmail.com'; // My email 

$list_id = '6c240604ff'; // After creating your Mailchimp list, this id will be generated and then you will use it to add new user to your list or to send campaigns to all users in your mailchimp list. To find it, go to https://usX.api.mailchimp.com/playground/ where X is your data center number. Example : us20 

$api_key = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-usX'; // This is the API-key that you will find in your Mailchimp account 

$auth = base64_encode( 'user:'.$api_key ); // Use base64_encode to encode the API-key

$data_center = substr($api_key,strpos($api_key,'-')+1);

$create_list_data = array( // The following parameters are the request body parameters for creating list 
                'name'        => 'Client list', // Name is required : *name 
                'email_address' => $email, // Not required
                'contact'  => array( // Contact is required : *contact 
                    'company' => 'Ematic', 
                    'address1' => 'Rm. 5, 10F., No.2, Sec. 3, Civic Blvd, Zhongzheng District, Taipei City, 100', //Not required 
                    'zip' => '100', 
                    'city' => 'Hsinchu', 
                    'state' => 'Hsinchu', 
                    'country' => 'Taiwan', 
                    'phone' => '0909401815', 
                    'permission_reminder' => 'You'.'re receiving this email because you signed up for updates about Ematic', // permission_reminder is required : *permission_reminder  
                ),
                'campaign_defaults'  => array( //campaign_defaults is required : *campaign_defaults
                    'from_name' => 'Claude', 
                    'from_email' => $email, 
                    'subject' => '',
                    'language' => 'en',
                ),
                'email_type_option' => true, //Not required 
                'permission_reminder' => 'this is a test message' //Not required 
            );


$add_new_user_data = array( // The following parameters are the request body parameters for adding new users

  'members' => array( // members field is required : *members 
      ['email_address' => $email, 
      'status' => 'subscribed', 
      'merge_fields' => array(
      'FNAME'=> 'Claude Vernet',
      'LNAME' => 'MICHEL',
      'PHONE' => '0909401815')
      ]
  ),
);


$send_campaign_data = array( // The following parameters are the request body parameters for sending campaigns 
  'recipients' => array( // recipients field is required : *required 
      'list_id' => $list_id),
  'type' => 'regular',
  'settings' => array(
    'subject_line' => 'Example Campaign', 
    'reply_to' => $email, 
    'from_name' => 'Customer Service'
  )
      
);


function create_list($create_list_data, $email, $api_key, $auth, $data_center){ // This function creates the Mailchimp list, it uses "create_list_data" to create our JSON data. All of this is possible because of this url : 'https://us20.api.mailchimp.com/3.0/lists' which allows us to create the list. After that we encode the JSON data, we use cUrl to create the session, at the end we've got the result. 
 
 
$url = 'https://us20.api.mailchimp.com/3.0/lists'; // This URL is used for creating a new mailchimp list, documentation: https://developer.mailchimp.com/documentation/mailchimp/reference/lists/#create-post_lists

$jsonString = json_encode($create_list_data); // json_encode converts $data to JSON code 

$curl = curl_init(); // Initializing cURL session

curl_setopt_array($curl, array( 

CURLOPT_URL => $url, // Set CURLOPT_URL option for a cURL transfer and the value is the URL for creating a new Mailchimp list

CURLOPT_HTTPHEADER =>array('Content-Type: application/json', // Here the value is the JSON application and the authentication for accessing Mailchimp
                              'Authorization: Basic '.$auth),

CURLOPT_RETURNTRANSFER => true, // It returns the transfer as a string

CURLOPT_TIMEOUT => 10, // Maximum number of seconds to allow cURL functions to execute, set to 10 

CURLOPT_POST => true, // Alternative port number to connect to

CURLOPT_SSL_VERIFYPEER => false, // Verify the peer's SSL certificate

CURLOPT_POSTFIELDS => $jsonString, // Post our JSON data 

));


$result = curl_exec($curl); // Take URL and pass it to the browser

$info = curl_getinfo($curl);  // info about cURL session 

$httpCode = curl_getinfo($curl , CURLINFO_HTTP_CODE); //Output HTTP_CODE info
  
curl_close($curl); // close cURL resource, and free up system resources

return $result; // Return the result 

}

function add_new_user($add_new_user_data, $email, $api_key, $list_id, $auth){ //This function adds new users to your Mailchimp list, again it is possible via this url: 'https://us20.api.mailchimp.com/3.0/lists/{list_id}' which allows us to add users. We also use cUrl to initialize the session and at the end new users are added to Mailchimp list. 


$url = 'https://us20.api.mailchimp.com/3.0/lists/'.$list_id; // // This URL is used for adding new users in your list. Documentation: https://developer.mailchimp.com/documentation/mailchimp/reference/lists/#create-post_lists_list_id


$jsonString = json_encode($add_new_user_data); // json_encode converts $data to JSON code 

$curl = curl_init(); // Initializing cURL session

curl_setopt_array($curl, array(

  CURLOPT_URL => $url, // Set CURLOPT_URL option for a cURL transfer and the value is the URL for creating a new Mailchimp list

  CURLOPT_RETURNTRANSFER => true, // It returns the transfer as a string

  CURLOPT_ENCODING => "", // Let it empty

  CURLOPT_MAXREDIRS => 10, //Maximum amount of HTTP redirections to follow

  CURLOPT_TIMEOUT => 30, // Maximum number of seconds to allow cURL functions to execute, set to 30

  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, // Force cURL to use HTTP_VERSION_1_1

  CURLOPT_CUSTOMREQUEST => 'POST', //A custom request method to use when doing a HTTP request

  CURLOPT_POSTFIELDS => $jsonString, // Post our JSON data 

  CURLOPT_HTTPHEADER =>array('Content-Type: application/json', // Here the value is the JSON application and the authentication for accessing Mailchimp

  'Authorization: Basic '.$auth),

));

$result = curl_exec($curl); // Take URL and pass it to the browser

$err = curl_error($curl); // cURl error 

curl_close($curl); // close cURL resource, and free up system resources

return $result; 

}


function send_campaign($send_campaign_data, $email, $api_key, $auth, $data_center, $list_id){//This funtion sends campaigns to users in your Mailchimp list, again, that is possible via this url: https://us20.api.mailchimp.com/3.0/campaigns' which allows us to send campaigns. We also use cUrl to initialize the session and post our JSON data. At the end, campaigns are sent to all users in Mailchimps list. 
 
$url = 'https://us20.api.mailchimp.com/3.0/campaigns'; // This URL is used for creating a new campaign to all users in your list, documentation: hhttps://developer.mailchimp.com/documentation/mailchimp/reference/campaigns/
 

$jsonString = json_encode($send_campaign_data); // json_encode converts $data to JSON code 
 

$curl = curl_init(); // Initializing cURL session

curl_setopt_array($curl, array( 

  CURLOPT_URL => $url, // Set CURLOPT_URL option for a cURL transfer and the value is the URL for creating a new Mailchimp list

  CURLOPT_HTTPHEADER =>array('Content-Type: application/json', // Here the value is the JSON application and the authentication for accessing Mailchimp
                              'Authorization: Basic '.$auth),

  CURLOPT_RETURNTRANSFER => true, // It returns the transfer as a string

  CURLOPT_TIMEOUT => 10, // Maximum number of seconds to allow cURL functions to execute, set to 10 

  CURLOPT_POST => true, // Alternative port number to connect to

  CURLOPT_SSL_VERIFYPEER => false, // Verify the peer's SSL certificate

  CURLOPT_POSTFIELDS => $jsonString, // Post our JSON data 

));


$result = curl_exec($curl); // Take URL and pass it to the browser

$info = curl_getinfo($curl);  // info about cURL session 

$httpCode = curl_getinfo($curl , CURLINFO_HTTP_CODE); //Output HTTP_CODE info

  
curl_close($curl); // close cURL resource, and free up system resources

return $result; // Return the result 

}

//Comment out the functions you will not be using, in this case, I call create list function 

create_list($create_list_data, $email, $api_key, $auth, $data_center); // Call create list function

//add_new_user($add_new_user_data, $email, $api_key, $list_id, $auth); //Call add new user function 

//send_campaign($send_campaign_data, $email, $api_key, $auth, $data_center, $list_id); // Call sending campaign function



?>
