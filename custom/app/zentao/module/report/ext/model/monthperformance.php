<?php
/**feature-1077**/
/**feature-1245**/
public function getOutputInfo($userRootId, $amibaName, $groupName, $account, $orgType, $monthNum)
{
    $outputInfoSql = $this->getOutputInfoSql($userRootId, $amibaName, $groupName, $account, $orgType, $monthNum);
    $outputInfo = $this->dao->query($outputInfoSql)->fetch();
    return $outputInfo;
}

public function getOutputInfoSql($userRootId, $amibaName, $groupName, $account, $orgType, $monthNum)
{
    if($orgType == 'dept')
    {
        $andWhereAmibaGroupAccount = "";
        $groupBy = "";
    }
    else if($orgType == 'amiba')
    {
        $andWhereAmibaGroupAccount = "and a.amiba_name = '$amibaName'";
        $groupBy = "group by a.amiba_name";
    }
    else if($orgType == 'group')
    {
        $andWhereAmibaGroupAccount = "and a.group_name = '$groupName'";
        $groupBy = "group by a.group_name";
    } 
    else
    {
        $andWhereAmibaGroupAccount = "and a.account = '$account'";
        $groupBy = "group by a.account";
    }
    
    $outputInfoSql = str_replace('#{monthNum}', $monthNum, $this->config->report->outputInfoSql);
    $outputInfoSql = str_replace('#{andWhereAmibaGroupAccount}', $andWhereAmibaGroupAccount, $outputInfoSql);
    $outputInfoSql = str_replace('#{groupBy}', $groupBy, $outputInfoSql);
    $outputInfoSql = str_replace('#{userRootId}', $userRootId, $outputInfoSql);
    
    return $outputInfoSql;
}