@extends('layouts.app')
@section('title', '相談記録 精神障害者向け')
@section('content')
    <div class="row">
        <h1 class="col-sm-12 text-center text-info mb-2">相談支援記録</h1>
        <h2 class="col-sm-12 text-center text-info mt-2">～ 精神障害者向け ～</h2>
    </div>
    <div id="mainVisual">
        <img src="{{ ('/images/network1.jpg') }}" alt="最初に表示されるtop画面">
    </div>
    <div class="row mt-5">
        {!! link_to_route('signup.get', '新規職員登録', [], ['class' => 'offset-sm-4 col-sm-4 btn btn-primary']) !!}
    </div>
    <div class="row mt-3">
        {!! link_to_route('login', 'ログイン画面', [], ['class' => 'offset-sm-4 col-sm-4 mt-2 btn btn-danger']) !!}
    </div>
@endsection