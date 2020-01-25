<?php
namespace Wisp\DAO;

/** Don't modify this file, this is auto generator by Wisp **/
class ShipBasic
{
    var $__schemaName = 'ship_basic';

    // 栏目表 
   static function AdminColumn()
   {
       require_once( __DIR__.'/ShipBasic/AdminColumn.php');
       return new ShipBasic\AdminColumn();
   }
    // 字段表 
   static function AdminField()
   {
       require_once( __DIR__.'/ShipBasic/AdminField.php');
       return new ShipBasic\AdminField();
   }
    //  
   static function AppInfo()
   {
       require_once( __DIR__.'/ShipBasic/AppInfo.php');
       return new ShipBasic\AppInfo();
   }
    //  
   static function PlatformFeedTemplate()
   {
       require_once( __DIR__.'/ShipBasic/PlatformFeedTemplate.php');
       return new ShipBasic\PlatformFeedTemplate();
   }
    //  
   static function SeqUid()
   {
       require_once( __DIR__.'/ShipBasic/SeqUid.php');
       return new ShipBasic\SeqUid();
   }
    // 支付订单 
   static function ShipAlipayOrder()
   {
       require_once( __DIR__.'/ShipBasic/ShipAlipayOrder.php');
       return new ShipBasic\ShipAlipayOrder();
   }
    // 装备建造包 
   static function ShipBuildEquipmentGroup()
   {
       require_once( __DIR__.'/ShipBasic/ShipBuildEquipmentGroup.php');
       return new ShipBasic\ShipBuildEquipmentGroup();
   }
    // 船建造包 
   static function ShipBuildGroup()
   {
       require_once( __DIR__.'/ShipBasic/ShipBuildGroup.php');
       return new ShipBasic\ShipBuildGroup();
   }
    // 船类型表 
   static function ShipBuildTitleClass()
   {
       require_once( __DIR__.'/ShipBasic/ShipBuildTitleClass.php');
       return new ShipBasic\ShipBuildTitleClass();
   }
    // 战役 
   static function ShipCampaign()
   {
       require_once( __DIR__.'/ShipBasic/ShipCampaign.php');
       return new ShipBasic\ShipCampaign();
   }
    // 战役 
   static function ShipCampaignHk()
   {
       require_once( __DIR__.'/ShipBasic/ShipCampaignHk.php');
       return new ShipBasic\ShipCampaignHk();
   }
    // 战役 
   static function ShipCampaignLevel()
   {
       require_once( __DIR__.'/ShipBasic/ShipCampaignLevel.php');
       return new ShipBasic\ShipCampaignLevel();
   }
    // 战役 
   static function ShipCampaignLevelHk()
   {
       require_once( __DIR__.'/ShipBasic/ShipCampaignLevelHk.php');
       return new ShipBasic\ShipCampaignLevelHk();
   }
    // CID映射表 
   static function ShipCidMap()
   {
       require_once( __DIR__.'/ShipBasic/ShipCidMap.php');
       return new ShipBasic\ShipCidMap();
   }
    // 装备表 
   static function ShipEquipment()
   {
       require_once( __DIR__.'/ShipBasic/ShipEquipment.php');
       return new ShipBasic\ShipEquipment();
   }
    // 装备建造 
   static function ShipEquipmentBuild()
   {
       require_once( __DIR__.'/ShipBasic/ShipEquipmentBuild.php');
       return new ShipBasic\ShipEquipmentBuild();
   }
    // 装备表 
   static function ShipEquipmentHk()
   {
       require_once( __DIR__.'/ShipBasic/ShipEquipmentHk.php');
       return new ShipBasic\ShipEquipmentHk();
   }
    // 事件表 
   static function ShipEvent()
   {
       require_once( __DIR__.'/ShipBasic/ShipEvent.php');
       return new ShipBasic\ShipEvent();
   }
    // 探索(远征) 
   static function ShipExplore()
   {
       require_once( __DIR__.'/ShipBasic/ShipExplore.php');
       return new ShipBasic\ShipExplore();
   }
    // 探索(远征) 
   static function ShipExploreHk()
   {
       require_once( __DIR__.'/ShipBasic/ShipExploreHk.php');
       return new ShipBasic\ShipExploreHk();
   }
    // 道具 
   static function ShipItem()
   {
       require_once( __DIR__.'/ShipBasic/ShipItem.php');
       return new ShipBasic\ShipItem();
   }
    // 道具 
   static function ShipItemHk()
   {
       require_once( __DIR__.'/ShipBasic/ShipItemHk.php');
       return new ShipBasic\ShipItemHk();
   }
    //  
   static function ShipLevelUser()
   {
       require_once( __DIR__.'/ShipBasic/ShipLevelUser.php');
       return new ShipBasic\ShipLevelUser();
   }
    // 登录奖励 
   static function ShipLoginAward()
   {
       require_once( __DIR__.'/ShipBasic/ShipLoginAward.php');
       return new ShipBasic\ShipLoginAward();
   }
    // 登录奖励宝箱 
   static function ShipLoginTreasurebox()
   {
       require_once( __DIR__.'/ShipBasic/ShipLoginTreasurebox.php');
       return new ShipBasic\ShipLoginTreasurebox();
   }
    //  
   static function ShipMedal()
   {
       require_once( __DIR__.'/ShipBasic/ShipMedal.php');
       return new ShipBasic\ShipMedal();
   }
    //  
   static function ShipMusic()
   {
       require_once( __DIR__.'/ShipBasic/ShipMusic.php');
       return new ShipBasic\ShipMusic();
   }
    //  
   static function ShipNotice()
   {
       require_once( __DIR__.'/ShipBasic/ShipNotice.php');
       return new ShipBasic\ShipNotice();
   }
    // pve章节 
   static function ShipPve()
   {
       require_once( __DIR__.'/ShipBasic/ShipPve.php');
       return new ShipBasic\ShipPve();
   }
    // pve活动图 
   static function ShipPveActive()
   {
       require_once( __DIR__.'/ShipBasic/ShipPveActive.php');
       return new ShipBasic\ShipPveActive();
   }
    // pve奖励 
   static function ShipPveAward()
   {
       require_once( __DIR__.'/ShipBasic/ShipPveAward.php');
       return new ShipBasic\ShipPveAward();
   }
    // 多舰队防卫 
   static function ShipPveGuard()
   {
       require_once( __DIR__.'/ShipBasic/ShipPveGuard.php');
       return new ShipBasic\ShipPveGuard();
   }
    //  
   static function ShipPveGuardLevel()
   {
       require_once( __DIR__.'/ShipBasic/ShipPveGuardLevel.php');
       return new ShipBasic\ShipPveGuardLevel();
   }
    // pve章节 
   static function ShipPveHk()
   {
       require_once( __DIR__.'/ShipBasic/ShipPveHk.php');
       return new ShipBasic\ShipPveHk();
   }
    // pve关卡 
   static function ShipPveLevel()
   {
       require_once( __DIR__.'/ShipBasic/ShipPveLevel.php');
       return new ShipBasic\ShipPveLevel();
   }
    // pve关卡 
   static function ShipPveLevelHk()
   {
       require_once( __DIR__.'/ShipBasic/ShipPveLevelHk.php');
       return new ShipBasic\ShipPveLevelHk();
   }
    // pve关卡结点 
   static function ShipPveLevelNode()
   {
       require_once( __DIR__.'/ShipBasic/ShipPveLevelNode.php');
       return new ShipBasic\ShipPveLevelNode();
   }
    // pveNpc 
   static function ShipPveNpc()
   {
       require_once( __DIR__.'/ShipBasic/ShipPveNpc.php');
       return new ShipBasic\ShipPveNpc();
   }
    // pve阵型 
   static function ShipPveNpcFormation()
   {
       require_once( __DIR__.'/ShipBasic/ShipPveNpcFormation.php');
       return new ShipBasic\ShipPveNpcFormation();
   }
    // pve阵型 
   static function ShipPveNpcFormationHk()
   {
       require_once( __DIR__.'/ShipBasic/ShipPveNpcFormationHk.php');
       return new ShipBasic\ShipPveNpcFormationHk();
   }
    // pveNpc 
   static function ShipPveNpcHk()
   {
       require_once( __DIR__.'/ShipBasic/ShipPveNpcHk.php');
       return new ShipBasic\ShipPveNpcHk();
   }
    // pve宝箱 
   static function ShipPveTreasurebox()
   {
       require_once( __DIR__.'/ShipBasic/ShipPveTreasurebox.php');
       return new ShipBasic\ShipPveTreasurebox();
   }
    // 注册邀请码 
   static function ShipRegcode()
   {
       require_once( __DIR__.'/ShipBasic/ShipRegcode.php');
       return new ShipBasic\ShipRegcode();
   }
    //  
   static function ShipRolename()
   {
       require_once( __DIR__.'/ShipBasic/ShipRolename.php');
       return new ShipBasic\ShipRolename();
   }
    //  
   static function ShipRolenameHk()
   {
       require_once( __DIR__.'/ShipBasic/ShipRolenameHk.php');
       return new ShipBasic\ShipRolenameHk();
   }
    // 规则表 
   static function ShipRule()
   {
       require_once( __DIR__.'/ShipBasic/ShipRule.php');
       return new ShipBasic\ShipRule();
   }
    // 船建造 
   static function ShipShipBuild()
   {
       require_once( __DIR__.'/ShipBasic/ShipShipBuild.php');
       return new ShipBasic\ShipShipBuild();
   }
    // 船卡 
   static function ShipShipCard()
   {
       require_once( __DIR__.'/ShipBasic/ShipShipCard.php');
       return new ShipBasic\ShipShipCard();
   }
    // 船卡 
   static function ShipShipCardHk()
   {
       require_once( __DIR__.'/ShipBasic/ShipShipCardHk.php');
       return new ShipBasic\ShipShipCardHk();
   }
    // 船升级表 
   static function ShipShipLevel()
   {
       require_once( __DIR__.'/ShipBasic/ShipShipLevel.php');
       return new ShipBasic\ShipShipLevel();
   }
    // 皮肤表 
   static function ShipShipSkin()
   {
       require_once( __DIR__.'/ShipBasic/ShipShipSkin.php');
       return new ShipBasic\ShipShipSkin();
   }
    // 强化 
   static function ShipShipStrengthen()
   {
       require_once( __DIR__.'/ShipBasic/ShipShipStrengthen.php');
       return new ShipBasic\ShipShipStrengthen();
   }
    // 船补给 
   static function ShipShipSupply()
   {
       require_once( __DIR__.'/ShipBasic/ShipShipSupply.php');
       return new ShipBasic\ShipShipSupply();
   }
    // 商城表 
   static function ShipShop()
   {
       require_once( __DIR__.'/ShipBasic/ShipShop.php');
       return new ShipBasic\ShipShop();
   }
    // 商城表 
   static function ShipShopHk()
   {
       require_once( __DIR__.'/ShipBasic/ShipShopHk.php');
       return new ShipBasic\ShipShopHk();
   }
    // 技能表 
   static function ShipSkill()
   {
       require_once( __DIR__.'/ShipBasic/ShipSkill.php');
       return new ShipBasic\ShipSkill();
   }
    // 技能buff表 
   static function ShipSkillBuff()
   {
       require_once( __DIR__.'/ShipBasic/ShipSkillBuff.php');
       return new ShipBasic\ShipSkillBuff();
   }
    // 技能buff表 
   static function ShipSkillBuffHk()
   {
       require_once( __DIR__.'/ShipBasic/ShipSkillBuffHk.php');
       return new ShipBasic\ShipSkillBuffHk();
   }
    // 技能表 
   static function ShipSkillHk()
   {
       require_once( __DIR__.'/ShipBasic/ShipSkillHk.php');
       return new ShipBasic\ShipSkillHk();
   }
    // 特殊影响类型 
   static function ShipSpecialEffect()
   {
       require_once( __DIR__.'/ShipBasic/ShipSpecialEffect.php');
       return new ShipBasic\ShipSpecialEffect();
   }
    // 战利品商店 
   static function ShipSpoilsShop()
   {
       require_once( __DIR__.'/ShipBasic/ShipSpoilsShop.php');
       return new ShipBasic\ShipSpoilsShop();
   }
    //  
   static function ShipSupportAtk()
   {
       require_once( __DIR__.'/ShipBasic/ShipSupportAtk.php');
       return new ShipBasic\ShipSupportAtk();
   }
    // 任务表 
   static function ShipTask()
   {
       require_once( __DIR__.'/ShipBasic/ShipTask.php');
       return new ShipBasic\ShipTask();
   }
    // 任务事件表 
   static function ShipTaskEvent()
   {
       require_once( __DIR__.'/ShipBasic/ShipTaskEvent.php');
       return new ShipBasic\ShipTaskEvent();
   }
    // 任务表 
   static function ShipTaskHk()
   {
       require_once( __DIR__.'/ShipBasic/ShipTaskHk.php');
       return new ShipBasic\ShipTaskHk();
   }
    // 注册的用户表 
   static function ShipUser()
   {
       require_once( __DIR__.'/ShipBasic/ShipUser.php');
       return new ShipBasic\ShipUser();
   }
    // 玩家头像 
   static function ShipUserAvatar()
   {
       require_once( __DIR__.'/ShipBasic/ShipUserAvatar.php');
       return new ShipBasic\ShipUserAvatar();
   }
    //  
   static function ShipUserPlatform()
   {
       require_once( __DIR__.'/ShipBasic/ShipUserPlatform.php');
       return new ShipBasic\ShipUserPlatform();
   }
    // 已经注册的角色名 
   static function ShipUsername()
   {
       require_once( __DIR__.'/ShipBasic/ShipUsername.php');
       return new ShipBasic\ShipUsername();
   }
    // 棱镜与渠道的uid 
   static function UidLjChannel()
   {
       require_once( __DIR__.'/ShipBasic/UidLjChannel.php');
       return new ShipBasic\UidLjChannel();
   }
    //  
   static function UidMap()
   {
       require_once( __DIR__.'/ShipBasic/UidMap.php');
       return new ShipBasic\UidMap();
   }}