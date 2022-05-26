<?php

namespace App\Http\Controllers;

use App\Record;
use App\Patient; // 追加
use App\Comment; // 追加
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RecordsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        // 注目する利用者とその相談記録一覧の情報を取得
        $patient = Patient::find($id);
        $records = $patient->records()->paginate(10);
        
         // キーワードは空文字の設定
        $keyword = '';
        
        // view の呼び出し
        // ある利用者とその相談記録一覧を表示させる
        return view('patients.records', compact('patient', 'records', 'keyword'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        // 新しいRecordインスタンスを作成し、注目する利用者の情報を取得
        $record = new Record();
        $patient = Patient::find($id);
        
        // view の呼び出し
        return view('patients.records_create', compact('record', 'patient'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // 入力された値の検証
        $this->validate($request, [
            'content' => 'required',
            'image' => [
                'file',
                'mimes:jpeg,jpg,png'
            ]
        ]);
        
        // 入力情報の取得
        $patient_id = $request->input('patient_id');
        $content = $request->input('content');
        $file =  $request->image;
        
        // S3用
        $path = Storage::disk('s3')->putFile('/uploads', $file, 'public');
 
        // パスから、最後の「ファイル名.拡張子」の部分だけ取得
        $image = basename($path);
        
        // 入力情報をもとに新しいpatientインスタンス作成
        \Auth::user()->add_record($patient_id, $content, $image);
       
        // トップページへリダイレクト
        return redirect('/patients/' . $patient_id . '/records')->with('flash_message', '新規相談記録を作成しました');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Record  $record
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id, $record_id)
    {
        // 注目する利用者とその相談記録の情報を取得
        $patient = Patient::find($id);
        $record = Record::find($record_id);
        
        // その利用者に紐づいた記録一覧を降順で取得
        $records = $patient->records()->get();
        
        // 注目している$recordが$recordsの何番目の配列要素か取得
        foreach($records as $key => $r){
            if($r->id === $record->id){
                $index = $key + 1;
                break;
            }
        }
        
        // 空のCommentモデル作成
        $comment = new Comment();
        // 注目する相談記録に紐づいたコメント一覧を降順で取得
        $comments = $record->comments()->orderBy('id', 'desc')->get();
        
        // 注目する相談記録にブックマークした人の一覧を取得
        $record_bookmark_users = $record->record_bookmark_users()->get();

        // view の呼び出し
        return view('records.show', compact('patient', 'record', 'index', 'comments', 'record_bookmark_users'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Record  $record
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id, $record_id)
    {
        // 注目する利用者とその相談記録の情報を取得
        $patient = Patient::find($id);
        $record = Record::find($record_id);
        // 加工した記録番号の取得
        $index = $request->input('index');
        
        // 注目している利用者の相談記録がログインしている職員が登録したものならば
        if($record->user->id === \Auth::id()){
            // view の呼び出し
            return view('records.edit', compact('patient', 'record', 'index'));
        } else {
            // ログイン前のトップ画面へリダイレクト
            return redirect('index');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Record  $record
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, $record_id)
    {
        // 注目している利用者とその相談記録の情報を取得
        $patient = Patient::find($id);
        $record = Record::find($record_id);
        // 加工した記録番号の取得
        $index = $request->input('index');
        
        // 注目している利用者の相談記録がログインしている職員が登録したものならば
        if($record->user->id === \Auth::id()) {
            // 入力値の検証
            $this->validate($request, [
                'content' => 'required',
                'image' => [
                    'file',
                    'mimes:jpeg,jpg,png'
                ]
            ]);
            
            // 入力情報の取得
            $patient_id = $request->input('patient_id');
            $content = $request->input('content');
            $file =  $request->image;
            
            // 画像ファイルのアップロード
            if($file){
                // S3用
                $path = Storage::disk('s3')->putFile('/uploads', $file, 'public');
         
                // パスから、最後の「ファイル名.拡張子」の部分だけ取得
                $image = basename($path);
            }else{
                // 画像を選択していなければ、画像ファイルは元の名前のまま
                $image = $record->image;
            }
            
            // 入力情報をもとにrecordインスタンス情報を更新
            $record->content = $content;
            $record->image = $image;
            
            // データベースを更新
            $record->save();
           
            // 注目する利用者の相談記録一覧へリダイレクト
            return redirect('/patients/' . $patient_id . '/records')->with('flash_message', $patient->name . 'の記録番号: ' . $index . 'を更新しました');
            
        } else {
            // ログイン前のトップ画面へリダイレクト
            return redirect('index');
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Record  $record
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id, $record_id)
    {
        // 注目している利用者とその相談記録の情報を取得
        $patient = Patient::find($id);
        $record = Record::find($record_id);
        $patient_id = $request->input('patient_id');
        // 加工した記録番号の取得
        $index = $request->input('index');
        
        // 注目している利用者の相談記録がログインしている職員が登録したものならば
        if($record->user->id === \Auth::id()) {
            // その相談記録の情報をデータベースから削除
            $record->delete($record_id);
            // リダイレクト
            return redirect('/patients/' . $patient_id . '/records')->with('flash_message', $patient->name . 'の相談記録を1件削除しました');
        } else {
            // ログイン前のトップ画面へリダイレクト
            return redirect('index');
        }
        
    }
    
    // 相談記録のキーワード検索
    public function search(Request $request, $id){
        
        // 注目する利用者の情報を取得
        $patient = Patient::find($id);
        
        // 入力された検索キーワードを取得
        $keyword = $request->input('keyword');

        // 相談記録のキーワード検索(注目する利用者IDの記録内容を検索できるように設定)
        $records = Record::where('content', 'like', '%' . $keyword . '%')->where('patient_id', $id)->paginate(10);
        
        // キーワードがなければフラッシュメッセージをnull
        if($keyword === null) {
            $flash_message = null;
        
        // キーワードがヒットしなければ
        } else if($records->count() === 0) {
            $flash_message = '検索キーワード: 『 ' . $keyword . ' 』に何もヒットしませんでした';
            
        } else {
            // フラッシュメッセージのセット
            $flash_message = '検索キーワード: 『 ' . $keyword . ' 』に' . $records->count() . '件ヒットしました';
        }
        
        // view の呼び出し
        return view('patients.records', compact('patient', 'records', 'keyword', 'flash_message'));

    }
}
