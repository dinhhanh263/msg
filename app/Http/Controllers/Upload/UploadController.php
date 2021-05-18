<?php

namespace App\Http\Controllers\Upload;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Library\CommonFunction;
use App\Http\Requests\Upload\UploadRequest;
use App\Models\UpdateFileList;

// AzureBlobのパッケージ
use League\Flysystem\Exception;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;
use MicrosoftAzure\Storage\Blob\Models\CreateBlockBlobOptions;


class UploadController extends Controller
{
	public function __construct()
	{
		// ログインチェック
		CommonFunction::checkLogin();
	}

	public function index()
	{
		// セッションからメッセージを取り出す
		$success_msg	= session()->get('success_msg');
		$error_msg		= session()->get('error_msg');

		// セッションを削除する
		session()->forget('success_msg');
		session()->forget('error_msg');

		// アップロードされているファイルを取得
		$ary_file_list = CommonFunction::urlList(10);

		return view('upload.index',
			[
				'success_msg'	=> $success_msg,
				'error_msg'		=> $error_msg,
				'ary_file_list'	=> $ary_file_list,
			]);
	}

	public function uploadFile(UploadRequest $request)
	{
		try {
			// ユーザー情報を取得
			$ary_user_data = session()->get('users');

			// .envのアクセスキーを呼び出す
			$connectionString = "DefaultEndpointsProtocol=https;AccountName=".config('azure.account_name').";AccountKey=".config('azure.account_key');

			$blobClient = BlobRestProxy::createBlobService($connectionString);

			// ファイルを取得
			$file = $request->file('pdf_file');

			// 送られてきたファイルを読み込む
			$content = fopen($file, "r");

			// ファイル名
			// ログイン名_年月日時分秒
			$file_name = $ary_user_data['groupCd']. '_' . date('YmdHis') . '.pdf';

			// DLではなく、ブラウザ表示にさせる
			$createBlockBlobOptions = new CreateBlockBlobOptions();
			$createBlockBlobOptions->setContentType("application/pdf");

			//ここで保存（コンテナ名、ファイル名、ファイル）
			$blobClient->createBlockBlob(config('azure.azure_container_name'), $file_name, $content, $createBlockBlobOptions);

			// DBに保存
			if(!$this->insert($file, $ary_user_data['groupCd'], $file_name))
			{
				throw new Exception();
			}

			session()->put('success_msg', 'アップロードが成功しました。');
		} catch (Exception $e) {
			session()->put('error_msg', 'アップロードに失敗しました。');
		}

		return redirect()->action('Upload\UploadController@index');
	}

	public function insert($file, $group_cd, $file_name)
	{
		// ファイル名を拡張子なしの形にする
		$tmp_file = $file->getClientOriginalName();
		$tmp_file = explode('.', $tmp_file);

		// insert処理
		return \App\Models\UpdateFileList::create([
			'label'			=> $tmp_file[0],	// アップロード時のファイル名
			'file_name'		=> $file_name,		// ファイル名
			'group_cd'		=> $group_cd,		// 部署コード
			'reg_date'		=> now(),			// 登録時間（*）
			'edit_date'		=> now(),			// 更新時間（*）
		]);
	}
}
