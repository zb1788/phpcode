<?php
namespace Home\Controller;
use Think\Controller;

class VueController extends Controller {
	public function login(){
		header('Access-Control-Allow-Origin:*');
		$username = I('username/s');
		$password = I('password/s');

		$str = $username.'|'.$password;

		$data = M('db_vue.user','t_')->where('username="%s" and pwd="%s"',$username,md5($password))->find();

		if(empty($data)){
			$info['code'] = 300;
			$info['data'] = '用户名或者密码错误';	
		}else{
			$res['token'] = $str;
			$res['username'] = $username;
			$info['code'] = 200;
			$info['data'] = $res;	
		}

		$this->ajaxReturn($info);
	}
	public function getUserInfo(){
		header('Access-Control-Allow-Origin:*');
		header('Access-Control-Allow-Headers:X-token');
		$token = I('token/s');
		//var_dump($_SERVER['HTTP_X_TOKEN']);exit;
		list($username,$password) = explode('|',$token);
		if($username == 'admin'){
			$roles[0] = $username;
		}else{
			$roles[0] = 'editor';
			$roles[1] = $username;
		}
		
		$data['roles'] = $roles;
		$data['avatar'] = '';
		$data['name'] = $username;
		$info['code'] = 200;
		$info['data'] = $data;
		$this->ajaxReturn($info);
	}

	public function getUserList(){
		header('Access-Control-Allow-Origin:*');
		$pageCurrent = I('pageCurrent');
		$pageSize = I('pageSize');

		$data = M('db_vue.user','t_')->limit(($pageCurrent-1)*$pageSize,$pageSize)->select();
		$total = M('db_vue.user','t_')->count();
		$info['code'] = 200;
		$info['data'] = $data;
		$info['total'] = $total;
		$this->ajaxReturn($info);
	}

	public function getUserInfos(){
		header('Access-Control-Allow-Origin:*');
		$id = I('id/d');
		$data =	M('db_vue.user','t_')->where('id="%d"',$id)->find();
		$info['code'] = 200;
		$info['data'] = $data;
		$this->ajaxReturn($info);
	}


	public function addUser(){
		header('Access-Control-Allow-Origin:*');
		$username = I('username/s');
		$truename = I('truename/s');
		$password = I('password/s');
		$ifadmin = I('ifadmin/d');
		$id = I('id/d');
		$type = I('type/s');

		$data['username'] = $username;
		$data['truename'] = $truename;
		$data['ifadmin'] = $ifadmin;
	
		if($type == 'add'){
			$data['pwd'] = md5($password);
			M('db_vue.user','t_')->add($data);
		}else{
			M('db_vue.user','t_')->where('id="%d"',$id)->save($data);
		}
		

		$status['status'] = 'ok';
		$info['code'] = 200;
		$info['data'] = $status;
		$this->ajaxReturn($info);
	}

	public function delUser(){
		header('Access-Control-Allow-Origin:*');
		$id = I('id/d',0);
		M('db_vue.user','t_')->where('id="%d"',$id)->delete();

		$status['status'] = 'ok';
		$info['code'] = 200;
		$info['data'] = $status;
		$this->ajaxReturn($info);
	}


	public function logout(){
		header('Access-Control-Allow-Origin:*');
		header('Access-Control-Allow-Headers:X-token');
		$token = I('token/s');
		

		$info['code'] = 200;
		$info['data'] = '';
		$this->ajaxReturn($info);
	}
	public function tablelist(){
		header('Access-Control-Allow-Origin:*');
		$token = I('token/s');
		
		$str = '{
  "code": 200,
  "data": {
    "items": [
      {
        "id": "650000198205268068",
        "title": "Ifn lkxisymrc mim ofui nrcksiini nzs vrtdhwdv ylghdrjhg xlkilcmxa dlocetrq wkdgdnj wtkdsxv fxdu ydxrynipq anly ykyxyo ufstvjei bhbgwv.",
        "status": "deleted",
        "author": "name",
        "display_time": "2007-02-04 02:08:58",
        "pageviews": 2399
      },
      {
        "id": "520000197807167156",
        "title": "Ogditgboec empu lbgld umy cdpekhgy biokb wtowd vlkoabimvd jjbspc geuohz ptfhqbvc lxfgx kigdus kddxsol rpdi.",
        "status": "published",
        "author": "name",
        "display_time": "1972-08-26 05:15:17",
        "pageviews": 1385
      },
      {
        "id": "61000020090621732X",
        "title": "Dikyuw vmfrc anovuncdbx ncozjtoui oxdgefun llwpefclu sgnz nisety xtknpyj xzhwkufqbu ipdtvjlp qseebrxpow edvkk npyfvwy.",
        "status": "draft",
        "author": "name",
        "display_time": "2009-03-02 03:08:23",
        "pageviews": 4428
      },
      {
        "id": "12000020030717312X",
        "title": "Mpkcloxi vpsucb rtluf anyjs xekg mvvfbyun byemmbbl mgxvaqe bsodedf tvb bpgqxe wndmz mppbtbyvr pjqwviv jnzr mtosd uqne ygphmkcz.",
        "status": "draft",
        "author": "name",
        "display_time": "1973-11-18 12:16:39",
        "pageviews": 4130
      },
      {
        "id": "330000200902171259",
        "title": "Sjaoi gqvhmo pkshsqxdgt mptf ikm tpcxcp fjxbltmxk qriu dnsr gimhr bpjs ohbnsmf froyzkch ykxr ykdeendqmt xgrkmus cqjrcy dhhtecv etrklswabu.",
        "status": "published",
        "author": "name",
        "display_time": "2007-03-10 09:27:44",
        "pageviews": 2601
      },
      {
        "id": "110000198103041879",
        "title": "Pebhbv sxlkhl nfenbvmb shlmnxflb pyddviudho skwm swucailldj bhfloef tbro vja fwme oaasi itzwqamy hyii.",
        "status": "draft",
        "author": "name",
        "display_time": "1999-02-12 08:16:28",
        "pageviews": 2259
      },
      {
        "id": "350000199411174832",
        "title": "Hensojsiq tjs bgbjzgjkd hceu joxgmk rrzpqe dsg tjm lmxxkre vnm yyphjgpnca txucdiq klen bcff.",
        "status": "published",
        "author": "name",
        "display_time": "1992-02-14 08:25:18",
        "pageviews": 2451
      },
      {
        "id": "320000199511018786",
        "title": "Exsd octffqnxp ulic rswu mgpnzi bioxux mwnqi cym uamqejdl pbsbwn plau hkuny.",
        "status": "published",
        "author": "name",
        "display_time": "1975-07-22 20:04:10",
        "pageviews": 545
      },
      {
        "id": "150000198410155187",
        "title": "Dsyr auieh loqvyxi zjsjvblthf gjfebcnj bnkxs rgyyzrihdv wswqkzx vmrkwsny klbps qgeptjvx xori btdsxt mnoykjh yhjrfj.",
        "status": "draft",
        "author": "name",
        "display_time": "2005-03-20 09:16:35",
        "pageviews": 2458
      },
      {
        "id": "650000201304060080",
        "title": "Mwntledhn pmsdpm pxgrctzlk uug bqupcpkr mtvbhyy tcpek deunzqd rbnisj ytbtwiiln hlbyi gktuipf dgscfnhqd sxqyin pkvu.",
        "status": "draft",
        "author": "name",
        "display_time": "1974-05-22 17:15:31",
        "pageviews": 2992
      },
      {
        "id": "450000200807055118",
        "title": "Vyuybsv perlrprnu dxleqf lkfqkki scwmyaf pvnvsmsutb nvu sgejbvdw dhlllm mceizqc ycksxu.",
        "status": "published",
        "author": "name",
        "display_time": "2009-09-15 15:06:58",
        "pageviews": 1598
      },
      {
        "id": "990000200011295648",
        "title": "Ifcituszn oepqkfjkcm qdg kthfwzzkqz yfpliww ileosldx wuepfucux mnuv xbwuby pjgssgkppu pgewvhwre abjlnet xmzikln ghs xodtzvx dlgerqprkr wzh djttt yjkbrj.",
        "status": "published",
        "author": "name",
        "display_time": "2000-03-25 01:25:59",
        "pageviews": 873
      },
      {
        "id": "710000199212138414",
        "title": "Jpmpjh fsetck axudiw jprkpv xbjwwe exu yhgxw uyakvsfh ajlmx ngsba tmtrls xuih.",
        "status": "draft",
        "author": "name",
        "display_time": "1998-05-12 17:18:38",
        "pageviews": 1923
      },
      {
        "id": "710000200205118189",
        "title": "Qkusdknx mvmxk ceqtvg vvfs hhml jzyib vfhigcor cctho vwxkbsov qngn vtqm temljd xdg.",
        "status": "draft",
        "author": "name",
        "display_time": "1974-07-12 12:40:31",
        "pageviews": 3120
      },
      {
        "id": "370000198211012301",
        "title": "Syptpzcp wubisj uosaat wdnmjjbtl ybnlbhtcix qmhuwop ege putpcm mxac bgopwhpt vakkms hgirscx cigeslv pbxrumsn.",
        "status": "draft",
        "author": "name",
        "display_time": "1993-09-09 19:27:28",
        "pageviews": 2825
      },
      {
        "id": "110000198504138743",
        "title": "Bqwrle iiitgtg otruu fqs vvodycxbvk ondl iujfgnbe ohfkjwho mxnhoqzwk tiiee grgosx nqriu ififtysfwe.",
        "status": "deleted",
        "author": "name",
        "display_time": "1995-06-09 21:05:27",
        "pageviews": 4039
      },
      {
        "id": "210000200803083133",
        "title": "Cgtmlou dywjfrj fuhdopqpc rycv wilemm tgrrouk cjeokllbw hhwolpral lutv pfwkczaw tbjlaqkye jnzjirffmt kpumbidjq qevwt tkidt xmnxulvn.",
        "status": "deleted",
        "author": "name",
        "display_time": "1991-02-12 10:36:32",
        "pageviews": 2915
      },
      {
        "id": "630000199910167300",
        "title": "Sowpgfv yojdyy vhkxmact osh wyqmevih tjjj rgysv esv pgprmuto uweeucnw imyo.",
        "status": "draft",
        "author": "name",
        "display_time": "1978-12-11 16:19:07",
        "pageviews": 4577
      },
      {
        "id": "430000198510094761",
        "title": "Bqtpqaw eptxnrbe xmosgsspf jqagyyv vce wzkdlg qbcbhm ixjnngx wipb wjagnbg afnls bevbynsh tgwg jwddpprnj oubpbq qwihihisr kvwun xnmcny.",
        "status": "deleted",
        "author": "name",
        "display_time": "2006-05-22 09:30:44",
        "pageviews": 2845
      },
      {
        "id": "43000019931128628X",
        "title": "Bnkmf vtpp vjjv esgpc pgscbtgdz cujjegcsi desjldo pnqf wskpgqxrf hhgj xlb zucmdjeg jlqq mhwf isoyc.",
        "status": "draft",
        "author": "name",
        "display_time": "2001-01-19 06:44:20",
        "pageviews": 4195
      },
      {
        "id": "340000199102228444",
        "title": "Ompgpitc hniuo fwugsmopi rwed qkpkkq sbcfyj pvpng kkrdejodj soetyogv lscs jbsogqeb clqskcqcl cuvzs vjafcqc iaq xtosolbf vrbolrdyq vlgcwehif.",
        "status": "draft",
        "author": "name",
        "display_time": "1985-06-08 10:32:58",
        "pageviews": 2922
      },
      {
        "id": "460000197804021294",
        "title": "Utvnwuqlx codyb pqigo myug dskmixchb wdxmbsmy eiwkrpmi kbwvchggjw apibyoq kapwdn qmrmgbwu npg bkqhs ayfuiinac dpixxypfh xijmlb beetqzbvt kuj fbvwdhpqef.",
        "status": "draft",
        "author": "name",
        "display_time": "1976-01-30 08:39:52",
        "pageviews": 1181
      },
      {
        "id": "140000198909193092",
        "title": "Iedkkh slgpm lqblftqk tnafsnfivl fnhjoii kdlcj itfhiflo tnwik kdte msfnoehon bctq smeheik pli istndr amwgvjm fzcesl kttshaw cljluv.",
        "status": "deleted",
        "author": "name",
        "display_time": "1984-01-05 09:18:04",
        "pageviews": 3980
      },
      {
        "id": "510000199807310677",
        "title": "Iupyaiomu ryr gbfxcw gxhgmc ieexyrtvsp gclb emoxfot quogfvnr ybuob zqb ljptkhkzb.",
        "status": "deleted",
        "author": "name",
        "display_time": "1971-03-30 19:42:49",
        "pageviews": 4961
      },
      {
        "id": "500000200702083520",
        "title": "Poencpm kugm itajo igpdvolfhh kdiqgymi fdehen frd wgb ijkrjj ylr wnykuz csgnsg tskrmysj uvqsugwxd jpgozcfliw nafolsvt xxm tguq fyxoz.",
        "status": "draft",
        "author": "name",
        "display_time": "1985-07-14 13:05:36",
        "pageviews": 2695
      },
      {
        "id": "430000201008235631",
        "title": "Wmattvpui kjiynw geqt qgh wkfthxu fmkqtsmizi yotvje fnlu rvdpqlrzn fjdnjrgfgt bxb wjmdioltwf ecfd vyst ahwkga ekayhhd grkdsn fjrelpms.",
        "status": "published",
        "author": "name",
        "display_time": "1979-05-12 16:17:11",
        "pageviews": 2592
      },
      {
        "id": "810000200304047674",
        "title": "Wrxvoipcky cxirgwstn kyylqpib skwerpcn phelbcuv rccisr cixffl drew zvizbge bkcmdwg.",
        "status": "deleted",
        "author": "name",
        "display_time": "2000-09-15 16:15:25",
        "pageviews": 1078
      },
      {
        "id": "530000198107226174",
        "title": "Dbyodi cdqb vro vkvpnpprnq ddejcfrywc maidjesc czsmedcb jbyr lujwkrg iyysinqr ehcc ekmnrxovd hkenmf tiosdzst mrre.",
        "status": "published",
        "author": "name",
        "display_time": "1981-09-26 12:35:55",
        "pageviews": 2919
      },
      {
        "id": "310000198201172757",
        "title": "Vkcmm nooenkk wbwernen gyjx konzokgc uxvpymt zpcvxkyayf tfpxeo rwogewxr uphbyyy gsppvglhep jdg zuyvq ivrhbo jtuqjvsdol lzkdvnob.",
        "status": "draft",
        "author": "name",
        "display_time": "2005-01-28 01:53:25",
        "pageviews": 2694
      },
      {
        "id": "310000200708276344",
        "title": "Hwwp btnspaqdp rpcbmnna okphrcgsq hqtcobzaml vfxwoh ulx oxyf uxeafsccs dgldvuvlg wyayyk ddjif.",
        "status": "draft",
        "author": "name",
        "display_time": "1997-06-18 19:46:45",
        "pageviews": 4809
      }
    ]
  }
}';
	echo $str;
	}
    public function getData(){
    	$json = '{"my":[{"id":"198","username":"\u5c0f\u59da","ks_code":"00010203020402","filename":"\u6211\u7231\u5b66\u8bed\u6587","filepath":"41D314BE-940E-42AA-9540-14B6A8656EB7.mp3","type":"1","praise":"0","addtime":"2017-11-14 13:18:39","listencount":"1","ispraise":"0","icon":"1"},{"id":"197","username":"\u5c0f\u59da","ks_code":"00010203020402","filename":"\u6211\u4e0a\u5b66\u4e86","filepath":"AC089802-2B8B-4377-860B-75B0E9C14215.mp3","type":"1","praise":"0","addtime":"2017-11-14 13:17:35","listencount":"2","ispraise":"0","icon":"1"},{"id":"196","username":"\u5c0f\u59da","ks_code":"00010203020402","filename":"\u6211\u4e0a\u5b66\u4e86","filepath":"4A87846D-FBA0-47BF-B1BE-58AC6DFC5E71.mp3","type":"2","praise":"0","addtime":"2017-11-14 13:16:25","listencount":"1","ispraise":"0","icon":"1"}],"siblings":[{"id":"14","username":"\u5f20\u535a","ks_code":"00010203020402","filename":"\u6211\u4e0a\u5b66\u4e86","filepath":"1503040146914.amr","type":"1","praise":"0","addtime":"2017-08-18 15:11:26","listencount":"9","ispraise":"0","icon":"1"},{"id":"18","username":"\u6d4b\u8bd5\u833c\u84bf","ks_code":"00010203020402","filename":"\u6211\u4e0a\u5b66\u4e86","filepath":"B3DECF42-78F7-4FDE-ADED-06AD7A2F5B19.amr","type":"2","praise":"0","addtime":"2017-09-01 10:54:33","listencount":"5","ispraise":"0","icon":"1"},{"id":"19","username":"\u6d4b\u8bd5\u833c\u84bf","ks_code":"00010203020402","filename":"\u6211\u4e0a\u5b66\u4e86","filepath":"01A649BA-E1E2-46A5-82D0-8FCFB98D6C30.amr","type":"2","praise":"0","addtime":"2017-09-01 10:57:00","listencount":"4","ispraise":"0","icon":"1"},{"id":"182","username":"\u6d4b\u8bd5\u6559\u5e08241","ks_code":"00010203020402","filename":"\u4e0a\u5b66\u6b4c","filepath":"1509951142235.amr","type":"1","praise":"0","addtime":"2017-11-06 14:15:16","listencount":"2","ispraise":"0","icon":"1"},{"id":"95","username":"\u53f2\u52aa\u6bd4","ks_code":"00010203020402","filename":"\u6211\u4e0a\u5b66\u4e86","filepath":"1507602448765.mp3","type":"2","praise":"0","addtime":"2017-10-10 09:51:34","listencount":"1","ispraise":"0","icon":"1"},{"id":"16","username":"\u738b\u65b9\u5bb9","ks_code":"00010203020402","filename":"\u6211\u4e0a\u5b66\u4e86","filepath":"1504063375730.amr","type":"2","praise":"0","addtime":"2017-08-30 11:24:52","listencount":"0","ispraise":"0","icon":"1"}],"other":[{"id":"14","username":"\u5f20\u535a","ks_code":"00010203020402","filename":"\u6211\u4e0a\u5b66\u4e86","filepath":"1503040146914.amr","type":"1","praise":"0","addtime":"2017-08-18 15:11:26","listencount":"9","ispraise":"0","icon":"1"},{"id":"18","username":"\u6d4b\u8bd5\u833c\u84bf","ks_code":"00010203020402","filename":"\u6211\u4e0a\u5b66\u4e86","filepath":"B3DECF42-78F7-4FDE-ADED-06AD7A2F5B19.amr","type":"2","praise":"0","addtime":"2017-09-01 10:54:33","listencount":"5","ispraise":"0","icon":"1"},{"id":"19","username":"\u6d4b\u8bd5\u833c\u84bf","ks_code":"00010203020402","filename":"\u6211\u4e0a\u5b66\u4e86","filepath":"01A649BA-E1E2-46A5-82D0-8FCFB98D6C30.amr","type":"2","praise":"0","addtime":"2017-09-01 10:57:00","listencount":"4","ispraise":"0","icon":"1"},{"id":"182","username":"\u6d4b\u8bd5\u6559\u5e08241","ks_code":"00010203020402","filename":"\u4e0a\u5b66\u6b4c","filepath":"1509951142235.amr","type":"1","praise":"0","addtime":"2017-11-06 14:15:16","listencount":"2","ispraise":"0","icon":"1"},{"id":"95","username":"\u53f2\u52aa\u6bd4","ks_code":"00010203020402","filename":"\u6211\u4e0a\u5b66\u4e86","filepath":"1507602448765.mp3","type":"2","praise":"0","addtime":"2017-10-10 09:51:34","listencount":"1","ispraise":"0","icon":"1"},{"id":"16","username":"\u738b\u65b9\u5bb9","ks_code":"00010203020402","filename":"\u6211\u4e0a\u5b66\u4e86","filepath":"1504063375730.amr","type":"2","praise":"0","addtime":"2017-08-30 11:24:52","listencount":"0","ispraise":"0","icon":"1"}]}';
    	$data = json_decode($json);
    	$this->ajaxReturn($data);
	}

}


