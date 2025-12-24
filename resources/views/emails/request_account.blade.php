<!DOCTYPE html>
<html>
<head>
    <title>Request for {{$accountDetail['type']}} Account</title>
</head>
<body>
<h1>Request to create {{strtolower($accountDetail['type'])}} account</h1>
<p>Account Detail :</p>
<p>
{{$accountDetail['type']}} name : {{$accountDetail['name']}} <br>
Email : {{$accountDetail['email']}} <br>
Phone : {{$accountDetail['phone']}} <br>
Address : {{$accountDetail['address']}} <br>
Postal Code : {{$accountDetail['postal_code']}} <br>
</p>

<p> <a href="{{route(strtolower($accountDetail['type']).'s.create')}}">Click here</a> to create account</p>
</body>
</html>
