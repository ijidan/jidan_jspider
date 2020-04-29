<?php

namespace Business\News;

use Exception;


/**
 * 微信公众号
 * Class WeChat
 * @package Business\News
 */
class WeChat extends NewsBase {

	public $platformsSubDir = ''; //子目录

	public $baseUrl = 'https://mp.weixin.qq.com/';


	public function computeListUrlList(){
		$content='{"file_cnt":{"app_msg_cnt":801,"app_msg_sent_cnt":443,"appmsg_template_cnt":0,"commondity_msg_cnt":0,"img_cnt":7076,"short_video_cnt":0,"total":7877,"video_cnt":0,"video_msg_cnt":11,"voice_cnt":0},"is_upload_cdn_ok":0,"item":[{"app_id":100009627,"author":"","can_open_reward":0,"create_time":"1587550518","data_seq":"1307949144513839105","digest":"深圳，真是例外？","file_id":0,"has_cps_product":0,"img_url":"https://mmbiz.qlogo.cn/mmbiz_jpg/DSoMmom3npCPWjZdkkXtpZRLpPm84IHlCoFpndUvlxKia3bzK00arfNWYM94RplcEJm9gnZ6YUpQ1X9g5hmcMFw/0?wx_fmt=jpeg","is_illegal":0,"is_sync_top_stories":0,"multi_item":[{"appmsg_album_info":{"album_id":0},"author":"","auto_elect_flag":0,"auto_elect_groups":"","auto_gen_digest":0,"can_reward":0,"categories_list":[],"cdn_1_1_url":"https://mmbiz.qlogo.cn/mmbiz_jpg/DSoMmom3npCPWjZdkkXtpZRLpPm84IHlia7f8dtxOjGkp83XydJdzJ5SM63Hgwb8lu14FyUlVSThok2a0FRQJww/0?wx_fmt=jpeg","cdn_235_1_url":"https://mmbiz.qlogo.cn/mmbiz_jpg/DSoMmom3npCPWjZdkkXtpZRLpPm84IHlCoFpndUvlxKia3bzK00arfNWYM94RplcEJm9gnZ6YUpQ1X9g5hmcMFw/0?wx_fmt=jpeg","cdn_url":"https://mmbiz.qlogo.cn/mmbiz_jpg/DSoMmom3npCPWjZdkkXtpZRLpPm84IHlCoFpndUvlxKia3bzK00arfNWYM94RplcEJm9gnZ6YUpQ1X9g5hmcMFw/0?wx_fmt=jpeg","cdn_url_back":"https://mmbiz.qpic.cn/mmbiz_png/mVPRddmic6vbJfHKCCpgKnVFutic6kqg9MxbAOV6CjzhEHyEHgQHkdJeiaccUJ6Uqz6Reto7elcw6c3A2JPXpRK6g/640?wx_fmt=png","copyright_type":0,"cover":"https://mmbiz.qlogo.cn/mmbiz_jpg/DSoMmom3npCPWjZdkkXtpZRLpPm84IHlCoFpndUvlxKia3bzK00arfNWYM94RplcEJm9gnZ6YUpQ1X9g5hmcMFw/0?wx_fmt=jpeg","del_flag":0,"digest":"深圳，真是例外？","file_id":0,"free_content":"","has_red_packet_cover":0,"is_mp_video":0,"is_new_video":0,"is_original":0,"is_pay_subscribe":0,"is_video_recommend":0,"more_read_info":{"article_list":[]},"need_open_comment":1,"only_fans_can_comment":false,"only_fans_days_can_comment":false,"ori_white_list":"","pay_desc":"","pay_feconfig":"","pay_fee":0,"pay_preview_percent":0,"related_video":[],"reward_money":0,"reward_wording":"","seq":0,"share_imageinfo":[],"share_page_type":0,"share_videoinfo":[],"share_voiceinfo":[],"show_cover_pic":0,"smart_product":0,"source_url":"https://hd.hinabian.com/Activity_Ad/index/10090?cid=quanxiangmuBPSHJ.hwfcyxgzh.yuanwen.20200325","tag_approved":true,"tagid":[],"tags":[],"title":"深圳房源一夜涨100万：政府救企业的钱，怎么成了炒房资本？","video_desc":""},{"appmsg_album_info":{"album_id":0},"author":"","auto_elect_flag":0,"auto_elect_groups":"","auto_gen_digest":0,"can_reward":0,"categories_list":[],"cdn_1_1_url":"https://mmbiz.qlogo.cn/mmbiz_jpg/DSoMmom3npCPWjZdkkXtpZRLpPm84IHlwu7crdIW5DXLHCgGzOOqKNZYcCzwbXcFicGBlwOAWDItiaykzqYZJnDg/0?wx_fmt=jpeg","cdn_235_1_url":"https://mmbiz.qlogo.cn/mmbiz_jpg/DSoMmom3npCPWjZdkkXtpZRLpPm84IHlwu7crdIW5DXLHCgGzOOqKNZYcCzwbXcFicGBlwOAWDItiaykzqYZJnDg/0?wx_fmt=jpeg","cdn_url":"https://mmbiz.qlogo.cn/mmbiz_jpg/DSoMmom3npCPWjZdkkXtpZRLpPm84IHlwu7crdIW5DXLHCgGzOOqKNZYcCzwbXcFicGBlwOAWDItiaykzqYZJnDg/0?wx_fmt=jpeg","cdn_url_back":"https://mmbiz.qpic.cn/mmbiz_jpg/8yriaPJs7ibJw7YG15DGIviaYeib8W5E38CicTAKkgQSMT7Ueet6qxfwYicUkIU5BCNMOaNMJPYBVHAib2CcaV09KjLzg/640?wx_fmt=jpeg","copyright_type":0,"cover":"https://mmbiz.qlogo.cn/mmbiz_jpg/DSoMmom3npCPWjZdkkXtpZRLpPm84IHlwu7crdIW5DXLHCgGzOOqKNZYcCzwbXcFicGBlwOAWDItiaykzqYZJnDg/0?wx_fmt=jpeg","del_flag":0,"digest":"实用手册。","file_id":0,"free_content":"","has_red_packet_cover":0,"is_mp_video":0,"is_new_video":0,"is_original":0,"is_pay_subscribe":0,"is_video_recommend":0,"more_read_info":{"article_list":[]},"need_open_comment":1,"only_fans_can_comment":false,"only_fans_days_can_comment":false,"ori_white_list":"","pay_desc":"","pay_feconfig":"","pay_fee":0,"pay_preview_percent":0,"related_video":[],"reward_money":0,"reward_wording":"","seq":1,"share_imageinfo":[],"share_page_type":0,"share_videoinfo":[],"share_voiceinfo":[],"show_cover_pic":0,"smart_product":0,"source_url":"https://hd.hinabian.com/Activity_Ad/index/10090?cid=quanxiangmuBPSHJ.hwfcyxgzh.yuanwen.20200325","tag_approved":true,"tagid":[],"tags":[],"title":"日本民宿新法，最强完整投资攻略！建议收藏","video_desc":""}],"publish_time":1587552115,"seq":0,"show_cover_pic":0,"title":"深圳房源一夜涨100万：政府救企业的钱，怎么成了炒房资本？","update_time":"1587552115","writerid":0},{"app_id":100009613,"author":"鲲鲲","can_open_reward":0,"create_time":"1587456629","data_seq":"1306523644566306816","digest":"英国财相开挂的人生。","file_id":0,"has_cps_product":0,"img_url":"https://mmbiz.qlogo.cn/mmbiz_jpg/DSoMmom3npDySGPcTlIXPBGdZudSkxB4k0wOpGovMfRaqPSEgxXEaTmDqK4SYxNtYOOKoHnor3NRuxCIG1DRpg/0?wx_fmt=jpeg","is_illegal":0,"is_sync_top_stories":0,"multi_item":[{"allow_reprint":0,"allow_reprint_modify":0,"appmsg_album_info":{"album_id":0},"author":"鲲鲲","auto_elect_flag":0,"auto_elect_groups":"","auto_gen_digest":0,"can_reward":0,"categories_list":[],"cdn_1_1_url":"https://mmbiz.qlogo.cn/mmbiz_jpg/DSoMmom3npDySGPcTlIXPBGdZudSkxB4Bg5eoxjhsUmrk3JLRFLgUTlausOvqfBrJaRQYwSeSECZib4AmTYWpdQ/0?wx_fmt=jpeg","cdn_235_1_url":"https://mmbiz.qlogo.cn/mmbiz_jpg/DSoMmom3npDySGPcTlIXPBGdZudSkxB4k0wOpGovMfRaqPSEgxXEaTmDqK4SYxNtYOOKoHnor3NRuxCIG1DRpg/0?wx_fmt=jpeg","cdn_url":"https://mmbiz.qlogo.cn/mmbiz_jpg/DSoMmom3npDySGPcTlIXPBGdZudSkxB4k0wOpGovMfRaqPSEgxXEaTmDqK4SYxNtYOOKoHnor3NRuxCIG1DRpg/0?wx_fmt=jpeg","cdn_url_back":"https://mmbiz.qlogo.cn/mmbiz_jpg/DSoMmom3npDySGPcTlIXPBGdZudSkxB4SibALERn31npxLnkoWPfsSDiaLNC4x4LYRUUUqNQibtiaRiba4PeicyibL8JQ/0?wx_fmt=jpeg","copyright_type":1,"cover":"https://mmbiz.qlogo.cn/mmbiz_jpg/DSoMmom3npDySGPcTlIXPBGdZudSkxB4k0wOpGovMfRaqPSEgxXEaTmDqK4SYxNtYOOKoHnor3NRuxCIG1DRpg/0?wx_fmt=jpeg","del_flag":0,"digest":"英国财相开挂的人生。","file_id":0,"free_content":"","has_red_packet_cover":0,"is_mp_video":0,"is_new_video":0,"is_original":0,"is_pay_subscribe":0,"is_video_recommend":0,"more_read_info":{"article_list":[]},"need_open_comment":1,"only_fans_can_comment":false,"only_fans_days_can_comment":false,"ori_white_list":"{&amp;quot;white_list&amp;quot;:[]}","original_article_type":"其他","pay_desc":"","pay_feconfig":"","pay_fee":0,"pay_preview_percent":0,"platform":"","related_video":[],"releasefirst":0,"releasetime":0,"reprint_permit_type":1,"reward_money":0,"reward_wording":"","seq":0,"share_imageinfo":[],"share_page_type":0,"share_videoinfo":[],"share_voiceinfo":[],"show_cover_pic":0,"smart_product":0,"source_url":"https://hd.hinabian.com/Activity_Ad/index/10090?cid=quanxiangmuBPSHJ.hwfcyxgzh.yuanwen.20200325","tag_approved":true,"tagid":[],"tags":[],"title":"“共产主义”延长到6月！起底英国财相：岳父是印度比尔·盖茨，人生开挂","video_desc":""},{"appmsg_album_info":{"album_id":0},"author":"大庄主","auto_elect_flag":0,"auto_elect_groups":"","auto_gen_digest":0,"can_reward":0,"categories_list":[],"cdn_1_1_url":"https://mmbiz.qlogo.cn/mmbiz_jpg/DSoMmom3npDySGPcTlIXPBGdZudSkxB420Cmib00OWicqr9kBbOuSNCS0NGoI26ln2j30qwkeUeibKIjTIxo7DTyg/0?wx_fmt=jpeg","cdn_235_1_url":"https://mmbiz.qlogo.cn/mmbiz_jpg/DSoMmom3npDySGPcTlIXPBGdZudSkxB420Cmib00OWicqr9kBbOuSNCS0NGoI26ln2j30qwkeUeibKIjTIxo7DTyg/0?wx_fmt=jpeg","cdn_url":"https://mmbiz.qlogo.cn/mmbiz_jpg/DSoMmom3npDySGPcTlIXPBGdZudSkxB420Cmib00OWicqr9kBbOuSNCS0NGoI26ln2j30qwkeUeibKIjTIxo7DTyg/0?wx_fmt=jpeg","cdn_url_back":"https://mmbiz.qlogo.cn/mmbiz_png/DSoMmom3npDySGPcTlIXPBGdZudSkxB4Abgw1gfCKiaRJCND1d8JAv8rtV8zpyG95sGZnx6s7cUsJHgFdW6MEow/0?wx_fmt=png","copyright_type":0,"cover":"https://mmbiz.qlogo.cn/mmbiz_jpg/DSoMmom3npDySGPcTlIXPBGdZudSkxB420Cmib00OWicqr9kBbOuSNCS0NGoI26ln2j30qwkeUeibKIjTIxo7DTyg/0?wx_fmt=jpeg","del_flag":0,"digest":"直播预告。","file_id":0,"free_content":"","has_red_packet_cover":0,"is_mp_video":0,"is_new_video":0,"is_original":0,"is_pay_subscribe":0,"is_video_recommend":0,"more_read_info":{"article_list":[]},"need_open_comment":1,"only_fans_can_comment":false,"only_fans_days_can_comment":false,"ori_white_list":"","pay_desc":"","pay_feconfig":"","pay_fee":0,"pay_preview_percent":0,"related_video":[],"reward_money":0,"reward_wording":"","seq":1,"share_imageinfo":[],"share_page_type":0,"share_videoinfo":[],"share_voiceinfo":[],"show_cover_pic":0,"smart_product":0,"source_url":"","tag_approved":true,"tagid":[],"tags":[],"title":"轻松置业菲律宾，还可以低门槛获得海外第二身份？","video_desc":""}],"publish_time":1587467148,"seq":1,"show_cover_pic":0,"title":"“共产主义”延长到6月！起底英国财相：岳父是印度比尔·盖茨，人生开挂","update_time":"1587467148","writerid":0},{"app_id":100009605,"author":"","can_open_reward":0,"create_time":"1587379776","data_seq":"1305064419025616897","digest":"魔幻世界。","file_id":0,"has_cps_product":0,"img_url":"https://mmbiz.qlogo.cn/mmbiz_jpg/DSoMmom3npA4xVlLyQJbwcD2tpmfCp5Eia9WvAsRtopFNxib1aojyTY0wozoIw985gqCSdVmjDsKt1mStpqxYjrA/0?wx_fmt=jpeg","is_illegal":0,"is_sync_top_stories":0,"multi_item":[{"appmsg_album_info":{"album_id":0},"author":"","auto_elect_flag":0,"auto_elect_groups":"","auto_gen_digest":0,"can_reward":0,"categories_list":[],"cdn_1_1_url":"https://mmbiz.qlogo.cn/mmbiz_jpg/DSoMmom3npA4xVlLyQJbwcD2tpmfCp5EULcUTMIIoscFdEQ6LfOMYCLWEFtXx1RFeusx1KRULHlxLn8qibygz1g/0?wx_fmt=jpeg","cdn_235_1_url":"https://mmbiz.qlogo.cn/mmbiz_jpg/DSoMmom3npA4xVlLyQJbwcD2tpmfCp5Eia9WvAsRtopFNxib1aojyTY0wozoIw985gqCSdVmjDsKt1mStpqxYjrA/0?wx_fmt=jpeg","cdn_url":"https://mmbiz.qlogo.cn/mmbiz_jpg/DSoMmom3npA4xVlLyQJbwcD2tpmfCp5Eia9WvAsRtopFNxib1aojyTY0wozoIw985gqCSdVmjDsKt1mStpqxYjrA/0?wx_fmt=jpeg","cdn_url_back":"https://mmbiz.qpic.cn/mmbiz_png/zwXibh0s2KGCIIeZXuQqcxyEibOAZicm3wAiaI2Q3XVgibw3YaBXibb0KicLjiahMwyEmpKVjgzI0tHk0gOcjdzVrREmjw/640?wx_fmt=png","copyright_type":0,"cover":"https://mmbiz.qlogo.cn/mmbiz_jpg/DSoMmom3npA4xVlLyQJbwcD2tpmfCp5Eia9WvAsRtopFNxib1aojyTY0wozoIw985gqCSdVmjDsKt1mStpqxYjrA/0?wx_fmt=jpeg","del_flag":0,"digest":"魔幻世界。","file_id":0,"free_content":"","has_red_packet_cover":0,"is_mp_video":0,"is_new_video":0,"is_original":0,"is_pay_subscribe":0,"is_video_recommend":0,"more_read_info":{"article_list":[]},"need_open_comment":1,"only_fans_can_comment":false,"only_fans_days_can_comment":false,"ori_white_list":"","pay_desc":"","pay_feconfig":"","pay_fee":0,"pay_preview_percent":0,"related_video":[],"reward_money":0,"reward_wording":"","seq":0,"share_imageinfo":[],"share_page_type":0,"share_videoinfo":[],"share_voiceinfo":[],"show_cover_pic":0,"smart_product":0,"source_url":"","tag_approved":true,"tagid":[],"tags":[],"title":"出狱后年薪千万，这个世界太魔幻了","video_desc":""}],"publish_time":1587380172,"seq":2,"show_cover_pic":0,"title":"出狱后年薪千万，这个世界太魔幻了","update_time":"1587380172","writerid":0},{"app_id":100009589,"author":"","can_open_reward":0,"create_time":"1587367288","data_seq":"1305037050386710530","digest":"不能盲目自信。","file_id":0,"has_cps_product":0,"img_url":"https://mmbiz.qlogo.cn/mmbiz_jpg/DSoMmom3npA4xVlLyQJbwcD2tpmfCp5EeoZR4DOib0NFJkf4ot4D5EMQbU977t0TqpKKoiawW0BuUwvEiblH1YSfw/0?wx_fmt=jpeg","is_illegal":0,"is_sync_top_stories":0,"multi_item":[{"appmsg_album_info":{"album_id":0},"author":"","auto_elect_flag":0,"auto_elect_groups":"","auto_gen_digest":0,"can_reward":0,"categories_list":[],"cdn_1_1_url":"https://mmbiz.qlogo.cn/mmbiz_jpg/DSoMmom3npA4xVlLyQJbwcD2tpmfCp5E8f62FN6e4HaxXZbBZPm2Tc8sEFOxPUNW4RpsDwcXnibWvjVumA12Cicg/0?wx_fmt=jpeg","cdn_235_1_url":"https://mmbiz.qlogo.cn/mmbiz_jpg/DSoMmom3npA4xVlLyQJbwcD2tpmfCp5EeoZR4DOib0NFJkf4ot4D5EMQbU977t0TqpKKoiawW0BuUwvEiblH1YSfw/0?wx_fmt=jpeg","cdn_url":"https://mmbiz.qlogo.cn/mmbiz_jpg/DSoMmom3npA4xVlLyQJbwcD2tpmfCp5EeoZR4DOib0NFJkf4ot4D5EMQbU977t0TqpKKoiawW0BuUwvEiblH1YSfw/0?wx_fmt=jpeg","cdn_url_back":"https://mmbiz.qpic.cn/mmbiz_jpg/dic0a1Ojr8BeQfEvHOVsAk7P790NzXgyf49bXAwJ4CVbYHHq2FfXI6VasVJ6Wst4g34eDtibJwnawLTEc6FSrb0Q/640?wx_fmt=jpeg","copyright_type":0,"cover":"https://mmbiz.qlogo.cn/mmbiz_jpg/DSoMmom3npA4xVlLyQJbwcD2tpmfCp5EeoZR4DOib0NFJkf4ot4D5EMQbU977t0TqpKKoiawW0BuUwvEiblH1YSfw/0?wx_fmt=jpeg","del_flag":0,"digest":"不能盲目自信。","file_id":0,"free_content":"","has_red_packet_cover":0,"is_mp_video":0,"is_new_video":0,"is_original":0,"is_pay_subscribe":0,"is_video_recommend":0,"more_read_info":{"article_list":[]},"need_open_comment":1,"only_fans_can_comment":false,"only_fans_days_can_comment":false,"ori_white_list":"","pay_desc":"","pay_feconfig":"","pay_fee":0,"pay_preview_percent":0,"related_video":[],"reward_money":0,"reward_wording":"","seq":0,"share_imageinfo":[],"share_page_type":0,"share_videoinfo":[],"share_voiceinfo":[],"show_cover_pic":0,"smart_product":0,"source_url":"https://hd.hinabian.com/Activity_Ad/index/10090?cid=quanxiangmuBPSHJ.hwfcyxgzh.yuanwen.20200325","tag_approved":true,"tagid":[],"tags":[],"title":"工信部部长：“中国制造”不像我们想象的那么强大，民间太狂热","video_desc":""},{"appmsg_album_info":{"album_id":0},"author":"大庄主","auto_elect_flag":0,"auto_elect_groups":"","auto_gen_digest":0,"can_reward":0,"categories_list":[],"cdn_1_1_url":"https://mmbiz.qlogo.cn/mmbiz_jpg/DSoMmom3npA4xVlLyQJbwcD2tpmfCp5E6A53SLJWGPDCtEvhuFgZtyaSWQcMicibMLYiajqyibHl3l7ooRGqHEobXA/0?wx_fmt=jpeg","cdn_235_1_url":"https://mmbiz.qlogo.cn/mmbiz_jpg/DSoMmom3npA4xVlLyQJbwcD2tpmfCp5E6A53SLJWGPDCtEvhuFgZtyaSWQcMicibMLYiajqyibHl3l7ooRGqHEobXA/0?wx_fmt=jpeg","cdn_url":"https://mmbiz.qlogo.cn/mmbiz_jpg/DSoMmom3npA4xVlLyQJbwcD2tpmfCp5E6A53SLJWGPDCtEvhuFgZtyaSWQcMicibMLYiajqyibHl3l7ooRGqHEobXA/0?wx_fmt=jpeg","cdn_url_back":"https://mmbiz.qpic.cn/mmbiz_png/4vOiaP9apA9fwmL5aqxdynaRfVzOKoetO7GkAMM6fVFSYdHYwV9P9UCp0SdFlnczogAO1x7WFMorichbcAdrIypQ/640?wx_fmt=png","copyright_type":0,"cover":"https://mmbiz.qlogo.cn/mmbiz_jpg/DSoMmom3npA4xVlLyQJbwcD2tpmfCp5E6A53SLJWGPDCtEvhuFgZtyaSWQcMicibMLYiajqyibHl3l7ooRGqHEobXA/0?wx_fmt=jpeg","del_flag":0,"digest":"性价比之王！","file_id":0,"free_content":"","has_red_packet_cover":0,"is_mp_video":0,"is_new_video":0,"is_original":0,"is_pay_subscribe":0,"is_video_recommend":0,"more_read_info":{"article_list":[]},"need_open_comment":1,"only_fans_can_comment":false,"only_fans_days_can_comment":false,"ori_white_list":"","pay_desc":"","pay_feconfig":"","pay_fee":0,"pay_preview_percent":0,"related_video":[],"reward_money":0,"reward_wording":"","seq":1,"share_imageinfo":[],"share_page_type":0,"share_videoinfo":[],"share_voiceinfo":[],"show_cover_pic":0,"smart_product":0,"source_url":"https://hd.hinabian.com/Activity_Ad/index/10090?cid=quanxiangmuBPSHJ.hwfcyxgzh.yuanwen.20200325","tag_approved":true,"tagid":[],"tags":[],"title":"曼谷第一富人区，房价5年涨幅66%，泰国最有钱11人都在这买房","video_desc":""}],"publish_time":1587378540,"seq":3,"show_cover_pic":0,"title":"工信部部长：“中国制造”不像我们想象的那么强大，民间太狂热","update_time":"1587378540","writerid":0},{"app_id":100009576,"author":"大庄主","can_open_reward":0,"create_time":"1587118781","data_seq":"1300764942181728256","digest":"一声叹息。","file_id":0,"has_cps_product":0,"img_url":"https://mmbiz.qlogo.cn/mmbiz_jpg/DSoMmom3npDBXqttgjrGLceCTibRCBhPtjWSrIlI1apzq8cTjYInBFoIOuLDia8tia759xNejYzGyauqq1RJNfAAQ/0?wx_fmt=jpeg","is_illegal":0,"is_sync_top_stories":0,"multi_item":[{"allow_reprint":0,"allow_reprint_modify":0,"appmsg_album_info":{"album_id":0},"author":"大庄主","auto_elect_flag":0,"auto_elect_groups":"","auto_gen_digest":0,"can_reward":0,"categories_list":[],"cdn_1_1_url":"https://mmbiz.qlogo.cn/mmbiz_jpg/DSoMmom3npDBXqttgjrGLceCTibRCBhPtibZExRVXksO4SoEibs3YV0eVBB69icx5gbBtNBiamw9B1bI2cg5bFTY4Aw/0?wx_fmt=jpeg","cdn_235_1_url":"https://mmbiz.qlogo.cn/mmbiz_jpg/DSoMmom3npDBXqttgjrGLceCTibRCBhPtjWSrIlI1apzq8cTjYInBFoIOuLDia8tia759xNejYzGyauqq1RJNfAAQ/0?wx_fmt=jpeg","cdn_url":"https://mmbiz.qlogo.cn/mmbiz_jpg/DSoMmom3npDBXqttgjrGLceCTibRCBhPtjWSrIlI1apzq8cTjYInBFoIOuLDia8tia759xNejYzGyauqq1RJNfAAQ/0?wx_fmt=jpeg","cdn_url_back":"https://mmbiz.qpic.cn/mmbiz_jpg/DSoMmom3npDBXqttgjrGLceCTibRCBhPtF4kibJ2989HtEo6vv3nzT1AON5iajnvfWdd5SguBTzGrLGjDeaWw8opg/640?wx_fmt=jpeg","copyright_type":1,"cover":"https://mmbiz.qlogo.cn/mmbiz_jpg/DSoMmom3npDBXqttgjrGLceCTibRCBhPtjWSrIlI1apzq8cTjYInBFoIOuLDia8tia759xNejYzGyauqq1RJNfAAQ/0?wx_fmt=jpeg","del_flag":0,"digest":"一声叹息。","file_id":0,"free_content":"","has_red_packet_cover":0,"is_mp_video":0,"is_new_video":0,"is_original":0,"is_pay_subscribe":0,"is_video_recommend":0,"more_read_info":{"article_list":[]},"need_open_comment":1,"only_fans_can_comment":false,"only_fans_days_can_comment":false,"ori_white_list":"{&amp;quot;white_list&amp;quot;:[]}","original_article_type":"房产","pay_desc":"","pay_feconfig":"","pay_fee":0,"pay_preview_percent":0,"platform":"","related_video":[],"releasefirst":0,"releasetime":0,"reprint_permit_type":1,"reward_money":0,"reward_wording":"","seq":0,"share_imageinfo":[],"share_page_type":0,"share_videoinfo":[],"share_voiceinfo":[],"show_cover_pic":0,"smart_product":0,"source_url":"https://hd.hinabian.com/Activity_Ad/index/10090?cid=quanxiangmuBPSHJ.hwfcyxgzh.yuanwen.20200325","tag_approved":true,"tagid":[],"tags":[],"title":"这个首富入狱16年，名下有264套北京房产，出狱后却发现…","video_desc":""}],"publish_time":1587123903,"seq":4,"show_cover_pic":0,"title":"这个首富入狱16年，名下有264套北京房产，出狱后却发现…","update_time":"1587123903","writerid":0}],"material_status":0,"search_cnt":0,"search_id":""}';
	}

	/**
	 * 抓取详情
	 * @param $id
	 * @return array|mixed
	 * @throws \ErrorException
	 * @throws Exception
	 */
	public function crawlDetail($id) {
		$fileName = __FUNCTION__ . '_id_' . $id;
		$url = $this->computeDetailPageUrl($id);
		$htmlContent = $this->fetchContent($fileName, $url);
		$title = $this->computeOnlyOneData($htmlContent, '.article-title');
		$abstract = '';

		//详情页替换
		$content = $this->computeHtmlContent($htmlContent, '.rich-text', 'first');
		$tagList=$this->computeData($htmlContent,'.ToolsLineView span');
		//入库
		$seqId = $this->doDetail(NewsBase::CAT1_TRENDS,NewsBase::CAT2_HOUSE_INVEST,$id, $title, $abstract, $content,$tagList);
		//图片入库
		$imgList = $this->extractImage($content, 'img', 'data-src');
		$this->doImage($seqId, $imgList);
		$detailData = [
			'id'       => $id,
			'title'    => $title,
			'abstract' => $abstract,
			'content'  => $content
		];
		return $detailData;
	}

	public function crawl() {
		$this->info('总页数抓取开始');
		$pageCnt = 1;
		$this->info("总页数抓取结束：一共 {$pageCnt} 页");
		if ($pageCnt) {
			for ($i = 1; $i <= $pageCnt; $i++) {
				$listPageUrl = $this->computeListPageUrl($i);
				//随机等待多少秒
				$this->waitRandomMS();
				try{
					$allId = $this->crawAllId($listPageUrl);
				}catch (\Exception $e){
					continue;
				}
				$this->info("列表抓取开始：第 {$i} 页");
				foreach ($allId as $id) {
					$this->info("项目详情抓取开始： ID为 $id");
					try{
						$this->crawlDetail($id);
					}catch (\Exception $e){
						continue;
					}
					$this->info("项目详情抓取结束： ID为 $id");
				}
			}
		}
	}

	/**
	 * 获取平台
	 * @return mixed
	 */
	public function getPlatform() {
		return 'wechat';
	}

	/**
	 * 计算列表页URL
	 * @param int $page
	 * @return mixed
	 */
	public function computeListPageUrl($page = 1) {
		// TODO: Implement computeListPageUrl() method.
	}

	/**
	 * 计算详情页URL
	 * @param $id
	 * @return mixed
	 */
	public function computeDetailPageUrl($id) {
		// TODO: Implement computeDetailPageUrl() method.
	}

	/**
	 * 爬取总数
	 * @param $url
	 * @return mixed
	 */
	public function crawlPageCnt($url) {
		// TODO: Implement crawlPageCnt() method.
	}

	/**
	 * 爬取所有ID
	 * @param $shortUrl
	 * @return mixed
	 */
	public function crawAllId($shortUrl) {
		// TODO: Implement crawAllId() method.
	}
}