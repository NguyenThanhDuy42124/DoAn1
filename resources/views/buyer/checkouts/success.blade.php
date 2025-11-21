<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Thanh toán thành công</title>
</head>
<body>
   @extends('layouts.account')

    @section('account_content')
    <h1>Thanh toán thành công!</h1>
    <a href="{{ route('products.list') }}" class="btn btn-primary">Tiếp tục mua hàng</a>
    @endsection
</body>
</html>