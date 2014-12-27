<?php

namespace Yan;

/**
 * 亲加通讯云
 * 
 * @author yanpeipan <yanpeipan_82@qq.com>
 * @version 2014.12.25
 * @link http://www.gotye.com.cn/docs/ime/restapi.html 亲加官方文档
 */
class Gotye {

    private $email = '';
    private $devpwd = '';
    public $appkey = '';

    const COMMAND_BASE_URL = 'https://qplusapi.gotye.com.cn:8443/api/';
    //用户管理
    const COMMAND_IMPORT_USERS = 'ImportUsers';
    const COMMAND_MODIFY_USER_PWD = 'ModifyUserPwd';
    const COMMAND_GET_USER_LIST = 'GetUserlist';
    const COMMAND_DEL_BLACK_LIST = 'DelBlacklist';
    const COMMAND_ADD_BLACK_LIST = 'AddBlacklist';
    const COMMAND_DISABLE_SAY = 'DisableSay';
    const COMMAND_GET_DISABLE_SAYS = 'GetDisableSays';
    //敏感词
    const COMMAND_SETUP_KEYWORD = 'SetupKeyword';
    const COMMAND_GET_KEYWORD = 'GetKeyword';
    //群管理
    const COMMAND_CREATE_GROUP = 'CreateGroup';
    const COMMAND_MODIFY_GROUP = 'ModifyGroup';
    const COMMAND_DISMISS_GROUP = 'DismissGroup';
    const COMMAND_GET_GROUP_USER_LIST = 'GetGroupUserList';
    const COMMAND_ADD_GROUP_MEMBER = 'AddGroupMember';
    const COMMAND_DEL_GROUP_MEMBER = 'DelGroupMember';
    const COMMAND_GET_GROUPS = 'GetGroups';
    const COMMAND_GET_GROUP_DETAIL = 'GetGroupDetail';
    //聊天室管理
    const COMMAND_CREATE_ROOM = 'CreateRoom';
    const COMMAND_DELETE_ROOM = 'DeleteRoom';
    const COMMAND_GET_ROOMS = 'GetRooms';
    //消息处理
    const COMMAND_SEND_MSG = 'SendMsg';
    const COMMAND_GET_MSG_HISTORY = 'GetMsgHistory';

    /**
     * 魔法函数
     * 用户调用用户管理，群管理之外的接口
     * 
     * @example new Gotye()->SetupKeyword(array('key_word' => '错误码,错误', 'setup_type' => '1'))
     * @param type $name
     * @param type $arguments
     * @return type
     */
    public function __call($name, $arguments) {

        if (isset($arguments[0])) {
            $arguments = $arguments[0];
        }
        return $this->request($name, $arguments);
    }

    /**
     * 发送请求
     * 
     * @param type $method
     * @param type $params
     * @return type
     */
    public function request($method, $params) {
        return $this->send($this->getUrl($method), $this->getPostFields($params));
    }

    /**
     * 密码加密
     * 密码长度6-16位
     * 
     * @param string $password
     * @return string $password
     */
    public function encrypt($password) {
        if (strlen($password) < 6) {
            throw new Exception('The length of Gotye Password must be in 6-16');
        } elseif (strlen($password) > 16) {
            $password = substr($password, 0, 16);
        }
        return $password;
    }

    /**
     * 返回开发者的账号和密码作为验证使用的信息，在每条协议中都作为基本数据传递。
     * 
     * @link http://www.gotye.com.cn/docs/ime/restapi.html#im_1 基本数据
     * @return array
     */
    public function getBaseData() {
        return array(
            'email' => $this->email,
            'devpwd' => $this->devpwd,
            'appkey' => $this->appkey
        );
    }

    /**
     * 链接地址
     * https://qplusapi.gotye.com.cn:8443/api/command  //其中commond替换为调用接口名
     * 
     * @param string $command
     * @return string
     */
    public function getUrl($command) {
        return self::COMMAND_BASE_URL . $command;
    }

    /**
     * 返回调用参数
     * 
     * @param array $data
     * @return string
     */
    public function getPostFields(array $data) {
        return json_encode(array_merge($data, $this->getBaseData()));
    }

    /**
     * 添加一个用户
     * 
     * @param string $useraccount
     * @param string $nickname
     * @param string $pwd
     * @return mix
     */
    public function addUser($useraccount, $nickname = '', $pwd = null) {
        $user = array('account' => $useraccount, 'nickname' => $nickname);
        if (isset($pwd)) {
            $user['pwd'] = $this->encrypt($pwd);
        }
        return $this->ImportUsers(array($user));
    }

    /**
     * 修改密码
     * 调用此接口用于修改带密码用户的密码，不支持批量。
     * 
     * @param string $useraccount
     * @param string $userpwd
     * @param string $olduserpwd
     * @return string
     */
    public function ModifyUserPwd($useraccount, $userpwd, $olduserpwd) {
        $data = array(
            'useraccount' => $useraccount,
            'userpwd' => $this->encrypt($userpwd),
            'olduserpwd' => $this->encrypt($olduserpwd)
        );
        return $this->send($this->getUrl(self::COMMAND_MODIFY_USER_PWD), $this->getPostFields($data));
    }

    /**
     * 导入用户
     * 调用此接口用于在选择授权注册时，批量导入用户。
     * 
     * @link http://www.gotye.com.cn/docs/ime/restapi.html#im_3 导入用户
     * @param array $users
     * @return type
     */
    public function ImportUsers(array $users) {
        $users = array('users' => $users);
        return $this->send($this->getUrl(self::COMMAND_IMPORT_USERS), $this->getPostFields($users));
    }

    /**
     * 获得用户列表
     * 
     * @param type $index 分页下标，必选项
     * @param type $count 分页的条数，可选项。默认为20
     */
    public function GetUserlist($index, $count = 20) {
        $data = array('index' => $index, 'count' => $count);
        return $this->send($this->getUrl(self::COMMAND_GET_USER_LIST), $this->getPostFields($data));
    }

    /**
     * 创建群
     * 调用此接口用于创建一个群
     * 
     * @param string $group_name 群名称，必选项
     * @param string $owner_type 所有类型，必选项。0为公开群，1为私有群
     * @param string $owner_account 所有者账号，必选项
     * @param string $approval 加入类型，必选项。0为自由加入，1为需要群主验证
     * @param string $group_head 群头像，可选项
     * @param string $group_info 群扩展信息，可选项。用于保存一些额外的信息，服务器不会对此信息做解析，在获取详情的时候可以拉取到
     * @return string {group_id:,errcode:}
     */
    public function CreateGroup($group_name, $owner_type, $owner_account, $approval, $group_head = null, $group_info = null) {
        $data = array(
            'group_name' => $group_name,
            'owner_type' => $owner_type,
            'owner_account' => $owner_account,
            'approval' => $approval,
            'group_head' => $group_head,
            'group_info' => $group_info
        );
        return $this->send($this->getUrl(self::COMMAND_CREATE_GROUP), $this->getPostFields($data));
    }

    /**
     * 解散群
     * 
     * @param string $group_id
     */
    public function DismissGroup($group_id) {
        $data = array('group_id' => $group_id);
        return $this->send($this->getUrl(self::COMMAND_DISMISS_GROUP), $this->getPostFields($data));
    }

    /**
     * 修改群信息
     * 调用此接口用于修改一个群的信息
     * 
     * @param string $group_id
     * @param array $options 
     * group_name 群名称，可选项 
     * group_head 群头像，可选项 
     * owner_type 所有类型，可选项。0为公开群，1为私有群 
     * owner_account  所有者账号，可选项 
     * approval 加入类型，可选项。
     * 0为自由加入，1为需要群主验证 
     * group_info 群扩展信息，可选项。用于保存一些额外的信息，服务器不会对此信息做解析，在获取详情的时候可以拉取到
     */
    public function ModifyGroup($group_id, array $options) {
        $data = array_merge(array('group_id' => $group_id), $options);
        return $this->send($this->getUrl(self::COMMAND_MODIFY_GROUP), $this->getPostFields($data));
    }

    /**
     * 获取群成员列表
     * 
     * @param string $group_id
     */
    public function GetGroupUserList($group_id) {
        $data = array('group_id' => $group_id);
        return $this->send($this->getUrl(self::COMMAND_GET_GROUP_USER_LIST), $this->getPostFields($data));
    }

    /**
     * 添加群成员
     * 调用此接口用于往群里添加一个成员，由于每个添加的新成员都会给群内已有的成员发送通知，为提高接口回应速度，这里暂不支持批量添加。
     * 
     * @param type $group_id
     * @param type $user_account
     */
    public function AddGroupMember($group_id, $user_account) {
        $data = array('group_id' => $group_id);
        return $this->send($this->getUrl(self::COMMAND_ADD_GROUP_MEMBER), $this->getPostFields($data));
    }

    /**
     * 删除群成员
     * 调用此接口用于往群里删除一个成员，由于每个删除的成员都会给群内已有的成员发送通知，为提高接口回应速度，这里暂不支持批量删除。
     * 
     * @param type $group_id
     * @param type $user_account
     */
    public function DelGroupMember($group_id, $user_account) {
        $data = array('group_id' => $group_id, 'user_account' => $user_account);
        return $this->send($this->getUrl(self::COMMAND_DEL_GROUP_MEMBER), $this->getPostFields($data));
    }

    /**
     * 获取群列表
     * 
     * @param type $last_group_id
     * @param type $count
     */
    public function GetGroups($last_group_id = 0, $count) {
        $data = array('last_group_id' => $last_group_id, 'count' => $count);
        return $this->send($this->getUrl(self::COMMAND_GET_GROUPS), $this->getPostFields($data));
    }

    /**
     * 获取群详情
     * 调用此接口用于获取某一个或者几个群的详情
     * 
     * @param array $group_id_list
     */
    public function GetGroupDetail(array $group_id_list) {
        $data = array('group_id_list' => $group_id_list);
        return $this->send($this->getUrl(self::COMMAND_GET_GROUP_DETAIL), $this->getPostFields($data));
    }

    /**
     * 发送请求
     * 
     * @param string  $method
     * @param mix $param
     */
    public function send($url, $param, $method = 'POST') {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

}
