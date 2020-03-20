<?php

namespace App\Controllers;

use Business\Test;
use Business\ULu;
use Business\WaiGF;
use Tools\Models\ArticleCategory;


/**
 * 站点相关
 * Class SiteController
 * @package App\Controllers
 */
class SiteController extends IndexController {
	/**
	 * 首页
	 * @return mixed
	 * @throws \Exception
	 */
	public function index() {
////		$test=new Test(null,false);
////		$data=$test->index();
////		dump($data,1);
		$wf=new WaiGF();
		$id=34578;
		$data=$wf->crawlDetail(4064,6091,$id);
		dump($data,1);
		$data=$wf->getNextHouseSeq();
		dump($data,1);
//		$data=$wf->crawl();
//		dump($data,1);
////		$data=$wf->crawlCountryCity();
////		$data=$wf->crawlPageCnt('/newhouselist_t1016_a0_m0_j0_o1.html');
////		$data=$wf->crawAllId();
		$id=36560;
		$data=$wf->crawlDetail($id);
		pr($data,1);
////		$obj= new ULu();
////		$id='563940838';
////		$detail=$obj->crawlDetail($id);
////		dump($detail,1);
////		$data=(new ULu())->crawlLocationRound($id);
////		dump($data,1);
		$businessList = [
			[
				"title"   => "MUA HÀNG TRỰC TUYẾN",
				"content" => "Mua hàng trực tuyến trên các website thương mại nổi tiếng thế giới của Trung Quốc như : Taobao , Tmall, 1688, alibaba…. Với chi phí rẻ nhất, hỗ trợ tương tác khách hàng nhanh nhất. Không chỉ riêng các webite nổi tiếng, nguồn hàng bạn cần là của doanh nghiệp Trung Quốc, chấp nhận thanh toán điện tử qua ngân hàng hoặc các tài khoản thương mại điện tử như alipay,…. Chúng tôi đều có thể hỗ trợ bạn ."
			],
			[
				"title"   => "TÌM KIẾM NGUỒN HÀNG",
				"content" => "Dịch vụ tìm kiếm nguồn hàng Trung Quốc: Chúng tôi sẽ hỗ trơ bạn :” Mua tận gốc, bán tận ngọn “ , giúp bạn tìm kiếm những nhà cung cấp sản phẩm Trung Quốc, hỗ trợ đàm phán, chuyển tiền, vận chuyển hàng hóa, giao dịch từ khi bạn bắt đầu có nhu cầu tìm kiếm nguồn hàng cho đến khi bạn tìm được sản phẩm ưng ý."
			],
			[
				"title"   => "DỊCH VỤ ĐẶT PHÒNG KHÁCH SẠN- PHƯƠNG TIỆN DI CHUYỂN",
				"content" => "Đặt phòng khách sạn, vé máy bay, vé tàu, vé xe : Bạn muốn đi công tác, đi chơi, đi tham quan, đi thăm thân, … nhưng còn đang băn khoăn về vấn đề đi lại? Hãy gọi cho chúng tôi để được phục vụ một cách đầy đủ nhất, hãy bỏ những lo lắng đó sang cho chúng tôi, để chúng tôi lo giúp bạn, còn việc của bạn chỉ là : “ Xách vali lên và đi thôi nào."
			],
			[
				"title"   => "VẬN CHUYỂN HÀNG HÓA",
				"content" => "Dịch vụ chuyển hàng Trung- ViệtChúng tôi là đơn vị vận tải chuyên nghiệp trong lĩnh vực vận chuyển hàng hóa từ Trung Quốc về Việt Nam , chỉ cần bạn có nhu cầu, chúng tôi đều cố gắng đáp ứng với số lượng không giới hạn cho mỗi đơn hàng, kể cả bạn muốn chuyển 1 cây kim, 1 chiếc bàn chải đánh răng… đối với chúng tôi đều ko thành vấn đề."
			],
			[
				"title"   => "KIỂM HÀNG",
				"content" => "Hỗ trợ kiểm tra hàng hóa : Chúng tôi cung cấp dịch vụ :” Check hàng trực tuyến “ , chúng tôi sẽ giúp bạn chụp ảnh, quay clip hoặc kiểm hàng theo yêu cầu của bạn, giúp bạn hạn chế đến mức tói đa rủi ro gặp phải khi mua hàng trực tuyến."
			],
			[
				"title"   => "CHUYỂN TIỀN",
				"content" => "Dịch vụ chuyển tiền Việt – Trung và Trung – Việt: chúng tôi có dịch vụ nhận chuyển tiền theo yêu cầu đến tài khoản chỉ định của khách hàng với chi phí hợp lý, tỷ giá ưu đãi cho khách hàng."
			],
			[
				"title"   => "NHẬP KHẨU HÀNG CHÍNH NGẠCH",
				"content" => "Dịch vụ nhập khẩu hàng Trung Quốc : Chúng tôi có cung cấp dịch vụ nhập khẩu hàng Trung Quốc theo đường chính ngạch để phục vụ cho nhu cầu đa dạng của khách hàng với chi phí hợp lý nhất."
			],
			[
				"title"   => "CUNG CẤP BIÊN – PHIÊN DỊCH",
				"content" => "Cung cấp biên- phiên dịch thời vụ, trong ngày :Hãy đến với chúng tôi, chúng tôi sẽ cung cấp cho bạn dịch vụ phiên dịch theo ngày, với đội ngũ phiên dịch viên uy tín và nhiều kinh nghiệm, chúng tôi tự tin sẽ đáp ứng được đầy đủ yêu cầu của bạn, góp phần giúp bạn có giao dịch thành công với các đối tác Trung Quốc."
			]
		];
		return $this->renderTemplate("site/index", ["businessList" => $businessList]);
	}

	/**
	 * 报价
	 * @return mixed
	 * @throws \Exception
	 */
	public function price() {
		$categoryId = ArticleCategory::ARTICLE_CATEGORY_PRICE;
		return $this->getArticleContainer($categoryId);
	}

	/**
	 * 协议
	 * @return mixed
	 * @throws \Exception
	 */
	public function agreement() {
		$categoryId = ArticleCategory::ARTICLE_CATEGORY_AGREEMENT;
		return $this->getArticleContainer($categoryId);
	}

	/**
	 * 介绍
	 * @return mixed
	 * @throws \Exception
	 */
	public function introduction() {
		$categoryId = ArticleCategory::ARTICLE_CATEGORY_INTRODUCTION;
		return $this->getArticleContainer($categoryId);
	}

	/**
	 * 容器
	 * @param $categoryId
	 * @return mixed
	 * @throws \Exception
	 */
	private function getArticleContainer($categoryId) {
		return $this->renderTemplate("site/content", ["categoryId" => $categoryId]);
	}

}