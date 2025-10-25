<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    @vite(['resources/css/usermanager.css', 'resources/js/app.js'])
    <title>Quản lý giỏ hàng - TechStore</title>
    @livewireStyles
</head>
<body>
    @extends('layouts.account')

    @section('account_content')
        @livewire('cart-manager')
    @endsection

    @livewireScripts
</body>
</html>