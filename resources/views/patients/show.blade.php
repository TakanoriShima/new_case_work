@extends('layouts.app')
@section('title', 'ID: ' . $patient->id . 'の詳細')
@section('content')           
    <div class="row mt-5">
        <h1 id="title" class="col-sm-12 text-center text-success mt-4 mb-3">ID: {{ $patient->id }}の登録情報の詳細</h1>
    </div>
    <div class="row mt-2">
        <p class="col-sm-12 text-left text-danger mt-4">※最終更新日: {{ $patient->updated_at }}</p>
    </div>
    <table class="table table-bordered table-striped">
        <tr>
            <th class="text-center col-3">登録日時</th>
            <td>{{ $patient->created_at }}</td>
        </tr>
        <tr>
            <th class="text-center col-3">名前</th>
            <td>{{ $patient->name }}</td>
        </tr>
        <tr>
            <th class="text-center">生年月日</th>
            <td>{{ $patient->birthday }}</td>
        </tr>
        <tr>
            <th class="text-center">性別</th>
            <td>{{ $patient->gender }}</td>
        </tr>
        <tr>
            <th class="text-center">郵便番号</th>
            <td>{{ $patient->postal_code }}</td>
        </tr>
        <tr>
            <th class="text-center">住所</th>
            <td>{{ $patient->address }}</td>
        </tr>
        <tr>
            <th class="text-center">電話番号</th>
            <td>{{ $patient->phone_number }}</td>
        </tr>
        <tr>
            <th class="text-center">メールアドレス</th>
            <td>{{ $patient->email }}</td>
        </tr>
        <tr>
            <th class="text-center">緊急連絡先</th>
            <td>{{ $patient->emergency_contact }}</td>
        </tr>
        <tr>
            <th class="text-center">緊急連絡先の住所</th>
            <td>{{ $patient->emergency_contact_address }}</td>
        </tr>
        <tr>
            <th class="text-center">医療機関</th>
            <td>{{ $patient->family_hospital }}</td>
        </tr>
        <tr>
            <th class="text-center">病名</th>
            <td>{{ $patient->disease_name }}</td>
        </tr>
        <tr>
            <th class="text-center">病歴</th>
            <td>{{ $patient->clinical_history }}</td>
        </tr>
        <tr>
            <th class="text-center">既往歴</th>
            <td>{{ $patient->medical_history }}</td>
        </tr>
        <tr>
            <th class="text-center">生活歴</th>
            <td>{{ $patient->life_history }}</td>
        </tr>
        <tr>
            <th class="text-center">家族構成</th>
            <td>{{ $patient->family_structure }}</td>
        </tr>
        <tr>
            <th class="text-center">学歴</th>
            <td>{{ $patient->academic_background }}</td>
        </tr>
        <tr>
            <th class="text-center">職歴</th>
            <td>{{ $patient->work_experience }}</td>
        </tr>
        <tr>
            <th class="text-center">経済状況</th>
            <td>{{ $patient->economic_condition }}</td>
        </tr>
        <tr>
            <th class="text-center">関係機関</th>
            <td>{{ $patient->related_organization }}</td>
        </tr>
        <tr>
            <th class="text-center">福祉サービス等</th>
            <td>{{ $patient->welfare_service }}</td>
        </tr>
        <tr>
            <th class="text-center">画像資料</th>
            <td class="text-center"><img src="{{ asset('uploads')}}/{{ $patient->image }}" alt="{{ $patient->image }}" id="patients_img"></td>
        </tr>
        <tr>
            <th class="text-center">支援にあたって配慮すべき点</th>
            <td>{{ $patient->consideration }}</td>
        </tr>
        <tr>
            <th class="text-center">その他</th>
            <td>{{ $patient->other }}</td>
        </tr>
    </table>
    
    <div class="row mt-5">
        {!! link_to_route('patients.edit', '編集', ['id' => $patient->id ], ['class' => 'offset-sm-4 col-sm-4 btn btn-success']) !!}
    </div>
    
    {!! Form::open(['route' => ['patients.destroy', 'id' => $patient->id ], 'method' => 'DELETE']) !!}
    <div class="row mt-4">
        {!! Form::submit('削除', ['class' => 'btn btn-danger btn-block offset-sm-4 col-sm-4']) !!}
    </div>
    {!! Form::close() !!}
    <div class="row mt-4 mb-5">
        {!! link_to_route('patients.index', '利用者一覧へ戻る', [], ['class' => 'offset-sm-4 col-sm-4 btn btn-info']) !!}
    </div>
        
@endsection