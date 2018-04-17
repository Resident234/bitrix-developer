<?php
	class Geo
    {
        public function __construct($options = null) {
            
            $this->dirname = dirname(__file__);
            
            // ip
            if(!isset($options['ip']) OR !$this->is_valid_ip($options['ip']))
                $this->ip = $this->get_ip(); 
            elseif($this->is_valid_ip($options['ip']))          
                $this->ip = $options['ip'];
            // ���������
            if(isset($options['charset']) && $options['charset'] /*&& $options['charset']!='windows-1251'*/)
                $this->charset = $options['charset'];
        }
        
        /**
         * ������� ��������� ���������� �������� �� ����������� ������� ������ �� ip
         * @param string - ���� �������. ���� ���������� ���������� ��������. 
         * ���� ����� ���� ������ 'inetnum', 'country', 'city', 'region', 'district', 'lat', 'lng'
         * @param bolean - ������������� ������� ������ � ���� ��� ���
         * ���� true, �� � ���� ����� �������� ������ �� ip � ��������� ������� �� ipgeobase ����������� �� �����.
         * ���� false, �� ������ ��������� ����� ������������� � ipgeobase
         * @return array OR string - ������������� ������� ����������� ������ �������.
         */
		 
        function get_value($key = false, $cookie = true)
        {
            $key_array = array('inetnum', 'country', 'city', 'region', 'district', 'lat', 'lng');
            if(!in_array($key, $key_array))
                $key = false;            
            
            // ���� ���������� ���� � �������� ��� �������, �� ������� � ���������� ������ �� ����
            if($cookie && isset($_COOKIE['geobase']))
            {
                $data = unserialize($_COOKIE['geobase']);
            } 
            else
            {
                $data = $this->get_geobase_data();
                setcookie('geobase', serialize($data), time()+3600*24*7); //������������� ���� �� ������
            }
            if($key)
                return $data[$key]; // ���� ������ ����, ���������� ������ � ������� �������
            else
                return $data; // ����� ���������� ������ �� ����� �������            
        }
        
        /**
         * ������� �������� ������ �� ip.
         * @return array - ���������� ������ � �������
         */

        function get_geobase_data()
        {
            // �������� ������ �� ip
            $link = 'http://ipgeobase.ru:7020/geo?ip='.$this->ip;
            //$link = 'http://ru.smart-ip.net/geoip-xml/'.$this->ip;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $link);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
            $string = curl_exec($ch);
            $data = $this->parse_string($string);
			//$data["new_charset"] = $this->charset;
            // ���� ������� ��������� �������� �� windows-1251, �������� ���������
            /*if($this->charset && count($data)>0)
			{
                //$string = iconv('windows-1251', $this->charset, $string);
                //$string = iconv('utf-8', 'windows-1251', $string);
                $data["region"] = iconv($data["charset"], $this->charset, $data["region"]);
                $data["city"] = iconv($data["charset"], $this->charset, $data["city"]);
                $data["district"] = iconv($data["charset"], $this->charset, $data["district"]);
			}*/
            return $data;
        }

        /**
         * ������� ������ ���������� � XML ������ � ������, ���� �� ������� �� ����������� ���������� Simplexml
         * @return array - ���������� ������ � �������
         */        
		 
        function parse_string($string)
        {
            $pa['inetnum'] = '#<inetnum>(.*)</inetnum>#is';
            $pa['country'] = '#<country>(.*)</country>#is';
            $pa['city'] = '#<city>(.*)</city>#is';
            $pa['region'] = '#<region>(.*)</region>#is';
            $pa['district'] = '#<district>(.*)</district>#is';
            $pa['lat'] = '#<lat>(.*)</lat>#is';
            $pa['lng'] = '#<lng>(.*)</lng>#is';
           /* $pa['inetnum'] = '#<host>(.*)</host>#is';
            $pa['country'] = '#<countryCode>(.*)</countryCode>#is';
            $pa['city'] = '#<city>(.*)</city>#is';
            $pa['region'] = '#<region>(.*)</region>#is';
            $pa['district'] = '#<countryName>(.*)</countryName>#is';
            $pa['lat'] = '#<latitude>(.*)</latitude>#is';
            $pa['lng'] = '#<longitude>(.*)</longitude>#is';
            $pa['charset'] = '#encoding="(.*)"#is';*/
            //$pa['charset'] = 'utf-8';
            $data = array();
            foreach($pa as $key => $pattern)
            {
                preg_match($pattern, $string, $out);
                if(isset($out[1]) && $out[1])
                $data[$key] = trim($out[1]);
            }
            return $data;
        }
        
        /**
         * ������� ���������� ip ����� �� ����������� ������� $_SERVER
         * ip ������ ����������� ������� � �������������, ��� ����������� ���������� ������������� ������
         * @return ip-�����
         */
		 
        function get_ip()
        {
            $ip = false;
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
                $ipa[] = trim(strtok($_SERVER['HTTP_X_FORWARDED_FOR'], ','));
            
            if (isset($_SERVER['HTTP_CLIENT_IP']))
                $ipa[] = $_SERVER['HTTP_CLIENT_IP'];       
            
            if (isset($_SERVER['REMOTE_ADDR']))
                $ipa[] = $_SERVER['REMOTE_ADDR'];
            
            if (isset($_SERVER['HTTP_X_REAL_IP']))
                $ipa[] = $_SERVER['HTTP_X_REAL_IP'];
            
            // ��������� ip-������ �� ���������� ������� � �������������.
            foreach($ipa as $ips)
            {
                //  ���� ip �������� �������� ����, ��������� ip ����� � ���������� ���
                if($this->is_valid_ip($ips))
                {                    
                    $ip = $ips;
                    break;
                }
            }
            return $ip;
            
        }        

        /**
         * ������� ��� �������� ���������� ip ������
         * @param ip ����� � ������� 1.2.3.4
         * @return bolean : true - ���� ip ��������, ����� false
         */
		 
        function is_valid_ip($ip=null)
        {
            if(preg_match("#^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$#", $ip))
                return true; // ���� ip-����� �������� ��� ���������� ���������, ���������� true
            
            return false; // ����� ���������� false
        }        
		 
	}
?>