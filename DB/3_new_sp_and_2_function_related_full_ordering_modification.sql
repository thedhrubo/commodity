-- phpMyAdmin SQL Dump
-- version 4.5.0.2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Sep 13, 2018 at 07:02 PM
-- Server version: 10.0.17-MariaDB
-- PHP Version: 5.6.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `commodity`
--

DELIMITER $$
--
-- Procedures
--


CREATE PROCEDURE `get_total_matching_diff_full` (IN `selectquery` MEDIUMTEXT, IN `daycount` INT, IN `inputprices` TEXT, IN `page_count` INT, IN `rows_count` INT)  BEGIN

declare pagelimit int default 1;
declare rowcount int default 1;
declare i int UNSIGNED default 0;
declare ii int UNSIGNED default 0;
declare j int UNSIGNED default 0;
declare k int UNSIGNED default 0;
declare m int UNSIGNED default 0;
declare qq1 varchar(60000) DEFAULT '';
declare qq MEDIUMTEXT DEFAULT '';
declare qqw MEDIUMTEXT DEFAULT '';
set @query=CONCAT('CREATE TEMPORARY TABLE IF NOT EXISTS temp AS (',selectquery,');');
#select qq1;

PREPARE stmt from @query;
EXECUTE stmt;
DEALLOCATE prepare stmt;




#select * from temp;
create TEMPORARY TABLE if not EXISTS inputpricestemp
(
id int UNSIGNED DEFAULT 0,
open_price decimal(8,2) DEFAULT 0,
highest_price decimal(8,2) DEFAULT 0,
lowest_price decimal(8,2) DEFAULT 0,
closed_price decimal(8,2) DEFAULT 0
);
set ii=0;
WHILE ii<daycount DO
set ii=ii+1;
if ii=1 then
set @inputprice=SUBSTRING_INDEX(inputprices,'|',1);
else
set @inputprice=SUBSTRING_INDEX(SUBSTRING_INDEX(inputprices,'|',ii),'|',-1);
end if;
insert into inputpricestemp
select ii,SUBSTRING_INDEX(@inputprice,',',1),SUBSTRING_INDEX(SUBSTRING_INDEX(@inputprice,',',2),',',-1),SUBSTRING_INDEX(SUBSTRING_INDEX(@inputprice,',',3),',',-1),SUBSTRING_INDEX(@inputprice,',',-1);

end WHILE;

#select * from inputpricestemp;


set @count=0;
WHILE i<daycount DO
set i=i+1;

set @inputopenprice1=(select open_price from inputpricestemp where id=i);
set @inputhighprice1=(select highest_price from inputpricestemp where id=i);
set @inputlowprice1=(select lowest_price from inputpricestemp where id=i);
set @inputcloseprice1=(select closed_price from inputpricestemp where id=i);
set j=0;
while j<daycount DO
set j=j+1;

set @inputopenprice2=(select open_price from inputpricestemp where id=j);
set @inputhighprice2=(select highest_price from inputpricestemp where id=j);
set @inputlowprice2=(select lowest_price from inputpricestemp where id=j);
set @inputcloseprice2=(select closed_price from inputpricestemp where id=j);


set k=0;
set @value1='';
set @value2='';
while k<4 do
set k=k+1;
if k=1 then
set @value1=CONCAT('open_price_',i);
set @inputvalue1=@inputopenprice1;
elseif k=2 then
set @value1=CONCAT('highest_price_',i);
set @inputvalue1=@inputhighprice1;
elseif k=3 then
set @value1=CONCAT('lowest_price_',i);
set @inputvalue1=@inputlowprice1;
else
set @value1=CONCAT('closed_price_',i);
set @inputvalue1=@inputcloseprice1;
end if;
set m=0;
#set @value1 = CONCAT('round(',@value1,',8)');
set @inputvalue1 = round(@inputvalue1,8);
while m<4 do
set m=m+1;
if m=1 then
set @value2=CONCAT('open_price_',j);
set @inputvalue2=@inputopenprice2;
elseif m=2 then
set @value2=CONCAT('highest_price_',j);
set @inputvalue2=@inputhighprice2;
elseif m=3 then
set @value2=CONCAT('lowest_price_',j);
set @inputvalue2=@inputlowprice2;
else
set @value2=CONCAT('closed_price_',j);
set @inputvalue2=@inputcloseprice2;
end if;
#set @value2 = CONCAT('round(',@value2,',8)');
set @inputvalue2 = round(@inputvalue2,8);
set @inputvalue=round(abs(((@inputvalue1-@inputvalue2)/@inputvalue1)*100),8);

if @value1!=@value2 then
set @value3=CONCAT('round(abs((((',@value1,'-',@value2,')/',@value1,')*100)),8)');


set qq=CONCAT(qq,'case when ',@value3,'=0 and ',@inputvalue,'=0 then 100 when','
greatest(',@value3,' ,',@inputvalue,')!=0 then round(100-((greatest(',@value3,',','',@inputvalue,')
-least(',@value3,',',@inputvalue,'))/greatest(',@value3,' ,',@inputvalue,'))*100,8) else ','round(100-((least(',@value3,' ,',@inputvalue,')-
greatest(',@value3,',',@inputvalue,'))/least(',@value3,' ,
',@inputvalue,'))*100,8) end +');

set @count=@count+1;
              

end if;
               
END WHILE;
               
END WHILE;
               
END WHILE;

END WHILE;
#set qq=@cal;
#select qq;
              
set qq=CONCAT('(',substring(qq,1,CHAR_LENGTH(qq)-1),')/',@count);
#set qq = CONCAT('round(',qq,',2)');
#select qq;
set @qr=CONCAT('update temp set outputdeviation=',qq);
#set qq=@qr;
#select qq from temp;  
prepare stmt1 from @qr;
EXECUTE stmt1;
DEALLOCATE prepare stmt1 ;
   set page_count=page_count-1;
set pagelimit=page_count*rows_count;
set rowcount=(select count(*) from temp);            
select *,rowcount as rowcount from temp order by outputdeviation desc limit pagelimit,rows_count;
drop table if exists inputpricestemp;
drop table if exists temp;
END$$

CREATE PROCEDURE `sp_detailed_fifteenth_analysis_closePrice` (IN `commodity` VARCHAR(100), IN `daycount` INT, IN `inputprices` TEXT, IN `diff` DOUBLE, IN `page_count` INT, IN `rows_count` INT)  BEGIN
declare pagelimit int default 1;
declare i int UNSIGNED default 0;
declare ii int UNSIGNED default 0;
declare iii int UNSIGNED default 0;
declare qq text DEFAULT '';
declare minlimit DOUBLE DEFAULT 0;
declare maxlimit DOUBLE DEFAULT 0;
declare inputopenprice DOUBLE DEFAULT 0;
declare inputhighprice DOUBLE DEFAULT 0;
declare inputlowprice DOUBLE DEFAULT 0;
declare inputcloseprice DOUBLE DEFAULT 0;
declare inputfinalcloseprice DOUBLE DEFAULT 0;
set @table_name='';
set @selectlist='';
set @selectlist1='';
set @selectlist2='';
set @selectlist3='';
set @wherelist1='';
set @wherelist2='';
set @wherelist3='';
set @wherelist4='';

create TEMPORARY TABLE if not EXISTS inputpricestemp
(
id int UNSIGNED DEFAULT 0,
open_price decimal(8,2) DEFAULT 0,
highest_price decimal(8,2) DEFAULT 0,
lowest_price decimal(8,2) DEFAULT 0,
closed_price decimal(8,2) DEFAULT 0
);
set ii=0;
WHILE ii<daycount DO
set ii=ii+1;
if ii=1 then
set @inputprice=SUBSTRING_INDEX(inputprices,'|',1);
else
set @inputprice=SUBSTRING_INDEX(SUBSTRING_INDEX(inputprices,'|',ii),'|',-1);
end if;
insert into inputpricestemp
select ii,SUBSTRING_INDEX(@inputprice,',',1),SUBSTRING_INDEX(SUBSTRING_INDEX(@inputprice,',',2),',',-1),SUBSTRING_INDEX(SUBSTRING_INDEX(@inputprice,',',3),',',-1),SUBSTRING_INDEX(@inputprice,',',-1);

if daycount=ii then
set inputfinalcloseprice=SUBSTRING_INDEX(@inputprice,',',-1);
end if;
end WHILE;


WHILE i<daycount DO
set i=i+1;
set @table_name=CONCAT(@table_name,commodity,' s',i,',');
set @selectlist=CONCAT(@selectlist,'s',i,'.','id id_',i,',s',i,'.','open_price open_price_',i,',s',i,'.','highest_price highest_price_'
,i,',s',i,'.','lowest_price lowest_price_',i,',s',i,'.','closed_price closed_price_',i,',s',i,'.','stockdate stock_date_',i,',s',i,'.','sellyear sellyear_',i,
',s',i,'.','sellmonth sellmonth_',i,',');
set @selectlist1=CONCAT(@selectlist1,'abs((((s',i,'.open_price - s',daycount,'.closed_price)/s',daycount,'.closed_price)*100))*1.0000000 open_deviation',i,',',
'abs((((s',i,'.highest_price - s',daycount,'.closed_price)/s',daycount,'.closed_price)*100))*1.0000000 high_deviation',i,',',
'abs((((s',i,'.lowest_price - s',daycount,'.closed_price)/s',daycount,'.closed_price)*100))*1.0000000 low_deviation',i,',',
'abs((((s',i,'.closed_price - s',daycount,'.closed_price)/s',daycount,'.closed_price)*100))*1.0000000 close_deviation',i,',');

if i<daycount then
set @wherelist1=CONCAT(@wherelist1,'s',i,'.','id+1=s',i+1,'.','id and ');
set @wherelist2=CONCAT(@wherelist2,'s',i,'.','sellyear=s',i+1,'.','sellyear and ');
set @wherelist3=CONCAT(@wherelist3,'s',i,'.','sellmonth=s',i+1,'.','sellmonth and ');
#set @wherelist4=CONCAT(@wherelist4,'fn_getpercent(','s',i,'.open_price,s',daycount,'.closed_price)',);
end if;


set inputopenprice=(select round(open_price,13) from inputpricestemp where id=i);
set inputhighprice=(select round(highest_price,13) from inputpricestemp where id=i);
set inputlowprice=(select round(lowest_price,13) from inputpricestemp where id=i);
set inputcloseprice=(select round(closed_price,13) from inputpricestemp where id=i);
set @inpopen=(((inputopenprice-inputfinalcloseprice)/inputfinalcloseprice)*100);
set @inphigh=(((inputhighprice-inputfinalcloseprice)/inputfinalcloseprice)*100);
set @inplow=(((inputlowprice-inputfinalcloseprice)/inputfinalcloseprice)*100);
set @inpclose=(((inputcloseprice-inputfinalcloseprice)/inputfinalcloseprice)*100);


set @minlimit1=@inpopen-diff;
set @maxlimit1=@inpopen+diff;

set @minlimit2=@inphigh-diff;
set @maxlimit2=@inphigh+diff;

set @minlimit3=@inplow-diff;
set @maxlimit3=@inplow+diff;

set @minlimit4=@inpclose-diff;
set @maxlimit4=@inpclose+diff;




set @selectlist2=CONCAT(@selectlist2,abs(round(@inpopen,13)),' as input_open_deviation',i,',',abs(round(@inphigh,13)),' as input_high_deviation',i,',',abs(round(@inplow,13)),' as input_low_deviation',i,',',abs(round(@inpclose,13)),' as input_close_deviation',i,',');

set @selectlist3=CONCAT(@selectlist3,'0 as outopendeviation',i,',','0 as outhigheviation',i,',','0 as outloweviation',i,',','0 as outclosedeviation',i,',');


set @wherelist4=CONCAT(@wherelist4,'(((s',i,'.open_price - s',daycount,'.closed_price)/s',daycount,'.closed_price)*100) between ',@minlimit1,' and ',@maxlimit1,' and '
,'(((s',i,'.highest_price - s',daycount,'.closed_price)/s',daycount,'.closed_price)*100)
between ',@minlimit2,' and ',@maxlimit2,' and ',
'(((s',i,'.lowest_price - s',daycount,'.closed_price)/s',daycount,'.closed_price)*100) between ',@minlimit3,' and ',@maxlimit3,' and ','
(((s',i,'.closed_price - s',daycount,'.closed_price)/s',daycount,'.closed_price)*100) between
',@minlimit4,' and ',@maxlimit4,' and');



END WHILE;
set @table_name=SUBSTRING(@table_name,1,CHAR_LENGTH(@table_name)-1);
#set @selectlist=SUBSTRING(@selectlist,1,CHAR_LENGTH(@selectlist)-1);
#set @selectlist2=SUBSTRING(@selectlist2,1,CHAR_LENGTH(@selectlist2)-1);
set @selectlist2=CONCAT(@selectlist2,'0000.00 as outputdeviation');
#set @wherelist3=SUBSTRING(@wherelist3,1,CHAR_LENGTH(@wherelist3)-4);
set @wherelist4=SUBSTRING(@wherelist4,1,CHAR_LENGTH(@wherelist4)-3);
set @wherelist=CONCAT(@wherelist1,@wherelist2,@wherelist3,@wherelist4);
#select qq;

if @wherelist!='' THEN
set @wherelist=CONCAT(' where ',@wherelist);
end if;
#set @wherelist='';
set @query=CONCAT('CREATE TEMPORARY TABLE IF NOT EXISTS a AS (SELECT ',@selectlist,@selectlist1,@selectlist2,' FROM ',@table_name,' ',@wherelist,');');
#select qq;


PREPARE stt1 from @query;
EXECUTE stt1;
#select 'sss';
DEALLOCATE prepare stt1;
#select * from a;
set iii=0;
set @update='';
set @finalselect='';
WHILE iii<daycount DO
set iii=iii+1;
set qq=CONCAT(qq,'
case when open_deviation',iii,'=0 and input_open_deviation',iii,'=0 then 100 when greatest(open_deviation',iii,',','input_open_deviation',iii,')!=0 then (100 -
((greatest(open_deviation',iii,',','input_open_deviation',iii,') - least(open_deviation',iii,',','input_open_deviation',iii,')) / greatest(open_deviation',iii,',','input_open_deviation',iii,')) * 100) else
(100 -
((least(open_deviation',iii,',','input_open_deviation',iii,') - greatest(open_deviation',iii,',','input_open_deviation',iii,')) / least(open_deviation',iii,',','input_open_deviation',iii,')) * 100) end
+
case when high_deviation',iii,'=0 and input_high_deviation',iii,'=0 then 100 when greatest(high_deviation',iii,',','input_high_deviation',iii,')!=0 then (100 -
((greatest(high_deviation',iii,',','input_high_deviation',iii,') - least(high_deviation',iii,',','input_high_deviation',iii,')) / greatest(high_deviation',iii,',','input_high_deviation',iii,')) * 100) else
(100 -
((least(high_deviation',iii,',','input_high_deviation',iii,') - greatest(high_deviation',iii,',','input_high_deviation',iii,')) / least(high_deviation',iii,',','input_high_deviation',iii,')) * 100) end
+
case when low_deviation',iii,'=0 and input_low_deviation',iii,'=0 then 100 when greatest(low_deviation',iii,',','input_low_deviation',iii,')!=0 then (100 -
((greatest(low_deviation',iii,',','input_low_deviation',iii,') - least(low_deviation',iii,',','input_low_deviation',iii,')) / greatest(low_deviation',iii,',','input_low_deviation',iii,')) * 100) else
(100 -
((least(low_deviation',iii,',','input_low_deviation',iii,') - greatest(low_deviation',iii,',','input_low_deviation',iii,')) / least(low_deviation',iii,',','input_low_deviation',iii,')) * 100) end
+
case when close_deviation',iii,'=0 and input_close_deviation',iii,'=0 then 100 when greatest(close_deviation',iii,',','input_close_deviation',iii,')!=0 then (100 -
((greatest(close_deviation',iii,',','input_close_deviation',iii,') - least(close_deviation',iii,',','input_close_deviation',iii,')) / greatest(close_deviation',iii,',','input_close_deviation',iii,')) * 100) else
(100 -
((least(close_deviation',iii,',','input_close_deviation',iii,') - greatest(close_deviation',iii,',','input_close_deviation',iii,')) / least(close_deviation',iii,',','input_close_deviation',iii,')) * 100) end
','+');

set @finalselect=CONCAT(@finalselect,'stock_date_',iii,',sellyear_',iii,',sellmonth_',iii,',');
#set @update2=CONCAT(@update2,'outopendeviation',iii,'+ outhighdeviation',iii,'+ outlowdeviation',iii+'+ outclosedeviation',iii
end WHILE;
#select * from a;
#set @res=0;
set qq=SUBSTRING(qq,1,CHAR_LENGTH(qq)-1);
#set qq=@update;
#select qq;
set @finalselect=CONCAT(@finalselect,'outputdeviation deviation ');
set @qr=CONCAT('update a set outputdeviation=(((',qq,')-100)/(',(daycount*4)-1,'))');
#select qq;
prepare stt2 from @qr;
EXECUTE stt2;
DEALLOCATE prepare stt2 ;


if page_count=0 and rows_count=0 THEN
set @limitq='';
else


set page_count=page_count-1;
set pagelimit=page_count*rows_count;
set @limitq=CONCAT('limit ',pagelimit,',',rows_count);
end if;
set @qrr=CONCAT('CREATE TEMPORARY TABLE IF NOT EXISTS finaltable AS (select ',@finalselect,' from a  order by deviation desc ',@limitq,' );');
prepare stt21 from @qrr;
EXECUTE stt21;
DEALLOCATE prepare stt21 ;

select * from finaltable;


#drop table inputpricestemp;
drop table if exists inputpricestemp;
drop table if exists a;
drop table if exists finaltable;

END$$

CREATE PROCEDURE `sp_getresult` (IN `commodity` VARCHAR(100), IN `daycount` INT, IN `inputprices` TEXT, IN `diff1` DOUBLE, IN `diff2` DOUBLE, IN `page_count` INT, IN `rows_count` INT)  BEGIN
declare pagelimit int default 1;
declare i int UNSIGNED default 0;
declare ii int UNSIGNED default 0;
declare iii int UNSIGNED default 0;
declare qq varchar(500000) DEFAULT '';
declare minlimit DOUBLE DEFAULT 0;
declare maxlimit DOUBLE DEFAULT 0;
declare inputopenprice DOUBLE DEFAULT 0;
declare inputhighprice DOUBLE DEFAULT 0;
declare inputlowprice DOUBLE DEFAULT 0;
declare inputcloseprice DOUBLE DEFAULT 0;
declare inputfinalcloseprice DOUBLE DEFAULT 0;
set @table_name='';
set @selectlist='';
set @selectlist1='';
set @selectlist2='';
set @selectlist3='';
set @wherelist1='';
set @wherelist2='';
set @wherelist3='';
set @wherelist4='';

create TEMPORARY TABLE if not EXISTS inputpricestemp
(
id int UNSIGNED DEFAULT 0,
open_price decimal(8,2) DEFAULT 0,
highest_price decimal(8,2) DEFAULT 0,
lowest_price decimal(8,2) DEFAULT 0,
closed_price decimal(8,2) DEFAULT 0
);
set ii=0;
WHILE ii<daycount DO
set ii=ii+1;
if ii=1 then
set @inputprice=SUBSTRING_INDEX(inputprices,'|',1);
else
set @inputprice=SUBSTRING_INDEX(SUBSTRING_INDEX(inputprices,'|',ii),'|',-1);
end if;
insert into inputpricestemp
select ii,SUBSTRING_INDEX(@inputprice,',',1),SUBSTRING_INDEX(SUBSTRING_INDEX(@inputprice,',',2),',',-1),SUBSTRING_INDEX(SUBSTRING_INDEX(@inputprice,',',3),',',-1),SUBSTRING_INDEX(@inputprice,',',-1);

if daycount=ii then
set inputfinalcloseprice=SUBSTRING_INDEX(@inputprice,',',-1);
end if;
end WHILE;


WHILE i<daycount DO
set i=i+1;
set @table_name=CONCAT(@table_name,commodity,' s',i,',');
set @selectlist=CONCAT(@selectlist,'s',i,'.','id id_',i,',s',i,'.','open_price open_price_',i,',s',i,'.','highest_price highest_price_'
,i,',s',i,'.','lowest_price lowest_price_',i,',s',i,'.','closed_price closed_price_',i,',s',i,'.','stockdate stock_date_',i,',s',i,'.','sellyear sellyear_',i,
',s',i,'.','sellmonth sellmonth_',i,',');

set @selectlist1=CONCAT(@selectlist1,'abs((((s',i,'.open_price - s',daycount,'.closed_price)/s',daycount,'.closed_price)*100)) open_deviation',i,',',
'abs((((s',i,'.highest_price - s',daycount,'.closed_price)/s',daycount,'.closed_price)*100)) high_deviation',i,',',
'abs((((s',i,'.lowest_price - s',daycount,'.closed_price)/s',daycount,'.closed_price)*100)) low_deviation',i,',',
'abs((((s',i,'.closed_price - s',daycount,'.closed_price)/s',daycount,'.closed_price)*100)) close_deviation',i,',');


if i<daycount then
set @wherelist1=CONCAT(@wherelist1,'s',i,'.','id+1=s',i+1,'.','id and ');
set @wherelist2=CONCAT(@wherelist2,'s',i,'.','sellyear=s',i+1,'.','sellyear and ');
set @wherelist3=CONCAT(@wherelist3,'s',i,'.','sellmonth=s',i+1,'.','sellmonth and ');
#set @wherelist4=CONCAT(@wherelist4,'fn_getpercent(','s',i,'.open_price,s',daycount,'.closed_price)',);
end if;


set inputopenprice=(select open_price from inputpricestemp where id=i);
set inputhighprice=(select highest_price from inputpricestemp where id=i);
set inputlowprice=(select lowest_price from inputpricestemp where id=i);
set inputcloseprice=(select closed_price from inputpricestemp where id=i);
set @inpopen=(((inputopenprice-inputfinalcloseprice)/inputfinalcloseprice)*100);
set @inphigh=(((inputhighprice-inputfinalcloseprice)/inputfinalcloseprice)*100);
set @inplow=(((inputlowprice-inputfinalcloseprice)/inputfinalcloseprice)*100);
set @inpclose=(((inputcloseprice-inputfinalcloseprice)/inputfinalcloseprice)*100);
if i=daycount or i=daycount-1 then
set @minlimit1=@inpopen-diff2;
set @maxlimit1=@inpopen+diff2;

set @minlimit2=@inphigh-diff2;
set @maxlimit2=@inphigh+diff2;

set @minlimit3=@inplow-diff2;
set @maxlimit3=@inplow+diff2;

set @minlimit4=@inpclose-diff2;
set @maxlimit4=@inpclose+diff2;
ELSE
set @minlimit1=@inpopen-diff1;
set @maxlimit1=@inpopen+diff1;

set @minlimit2=@inphigh-diff1;
set @maxlimit2=@inphigh+diff1;

set @minlimit3=@inplow-diff1;
set @maxlimit3=@inplow+diff1;

set @minlimit4=@inpclose-diff1;
set @maxlimit4=@inpclose+diff1;
end if;



set @selectlist2=CONCAT(@selectlist2,abs(@inpopen),' as input_open_deviation',i,',',abs(@inphigh),' as input_high_deviation',i,',',abs(@inplow),' as input_low_deviation',i,',',abs(@inpclose),' as input_close_deviation',i,',');

set @selectlist3=CONCAT(@selectlist3,'0 as outopendeviation',i,',','0 as outhigheviation',i,',','0 as outloweviation',i,',','0 as outclosedeviation',i,',');


set @wherelist4=CONCAT(@wherelist4,'(((s',i,'.open_price - s',daycount,'.closed_price)/s',daycount,'.closed_price)*100) between ',@minlimit1,' and ',@maxlimit1,' and '
,'(((s',i,'.highest_price - s',daycount,'.closed_price)/s',daycount,'.closed_price)*100)
between ',@minlimit2,' and ',@maxlimit2,' and ',
'(((s',i,'.lowest_price - s',daycount,'.closed_price)/s',daycount,'.closed_price)*100) between ',@minlimit3,' and ',@maxlimit3,' and ','
(((s',i,'.closed_price - s',daycount,'.closed_price)/s',daycount,'.closed_price)*100) between
',@minlimit4,' and ',@maxlimit4,' and');



END WHILE;
set @table_name=SUBSTRING(@table_name,1,CHAR_LENGTH(@table_name)-1);
#set @selectlist=SUBSTRING(@selectlist,1,CHAR_LENGTH(@selectlist)-1);
#set @selectlist2=SUBSTRING(@selectlist2,1,CHAR_LENGTH(@selectlist2)-1);
set @selectlist2=CONCAT(@selectlist2,'0000.00 as outputdeviation');
#set @wherelist3=SUBSTRING(@wherelist3,1,CHAR_LENGTH(@wherelist3)-4);
set @wherelist4=SUBSTRING(@wherelist4,1,CHAR_LENGTH(@wherelist4)-3);
set @wherelist=CONCAT(@wherelist1,@wherelist2,@wherelist3,@wherelist4);
#select qq;
if @wherelist!='' THEN
set @wherelist=CONCAT(' where ',@wherelist);
end if;
#set @wherelist='';
set @query=CONCAT('CREATE TEMPORARY TABLE IF NOT EXISTS a AS (SELECT ',@selectlist,@selectlist1,@selectlist2,' FROM ',@table_name,' ',@wherelist,');');
#select qq;

PREPARE stt1 from @query;
EXECUTE stt1;
#select 'sss';
DEALLOCATE prepare stt1;
#select * from a;
set iii=0;
set @update='';
set @finalselect='';
WHILE iii<daycount DO
set iii=iii+1;
set qq=CONCAT(qq,'
case when open_deviation',iii,'=0 and input_open_deviation',iii,'=0 then 100 when greatest(open_deviation',iii,',','input_open_deviation',iii,')!=0 then (100 -
((greatest(open_deviation',iii,',','input_open_deviation',iii,') - least(open_deviation',iii,',','input_open_deviation',iii,')) / greatest(open_deviation',iii,',','input_open_deviation',iii,')) * 100) else
(100 -
((least(open_deviation',iii,',','input_open_deviation',iii,') - greatest(open_deviation',iii,',','input_open_deviation',iii,')) / least(open_deviation',iii,',','input_open_deviation',iii,')) * 100) end
+
case when high_deviation',iii,'=0 and input_high_deviation',iii,'=0 then 100 when greatest(high_deviation',iii,',','input_high_deviation',iii,')!=0 then (100 -
((greatest(high_deviation',iii,',','input_high_deviation',iii,') - least(high_deviation',iii,',','input_high_deviation',iii,')) / greatest(high_deviation',iii,',','input_high_deviation',iii,')) * 100) else
(100 -
((least(high_deviation',iii,',','input_high_deviation',iii,') - greatest(high_deviation',iii,',','input_high_deviation',iii,')) / least(high_deviation',iii,',','input_high_deviation',iii,')) * 100) end
+
case when low_deviation',iii,'=0 and input_low_deviation',iii,'=0 then 100 when greatest(low_deviation',iii,',','input_low_deviation',iii,')!=0 then (100 -
((greatest(low_deviation',iii,',','input_low_deviation',iii,') - least(low_deviation',iii,',','input_low_deviation',iii,')) / greatest(low_deviation',iii,',','input_low_deviation',iii,')) * 100) else
(100 -
((least(low_deviation',iii,',','input_low_deviation',iii,') - greatest(low_deviation',iii,',','input_low_deviation',iii,')) / least(low_deviation',iii,',','input_low_deviation',iii,')) * 100) end
+
case when close_deviation',iii,'=0 and input_close_deviation',iii,'=0 then 100 when greatest(close_deviation',iii,',','input_close_deviation',iii,')!=0 then (100 -
((greatest(close_deviation',iii,',','input_close_deviation',iii,') - least(close_deviation',iii,',','input_close_deviation',iii,')) / greatest(close_deviation',iii,',','input_close_deviation',iii,')) * 100) else
(100 -
((least(close_deviation',iii,',','input_close_deviation',iii,') - greatest(close_deviation',iii,',','input_close_deviation',iii,')) / least(close_deviation',iii,',','input_close_deviation',iii,')) * 100) end
','+');

set @finalselect=CONCAT(@finalselect,'stock_date_',iii,',sellyear_',iii,',sellmonth_',iii,',');
#set @update2=CONCAT(@update2,'outopendeviation',iii,'+ outhighdeviation',iii,'+ outlowdeviation',iii+'+ outclosedeviation',iii
end WHILE;
#select * from a;
#set @res=0;
set qq=SUBSTRING(qq,1,CHAR_LENGTH(qq)-1);
#set qq=@update;
#select qq;
set @finalselect=CONCAT(@finalselect,'outputdeviation deviation ');
set @qr=CONCAT('update a set outputdeviation=(((',qq,')-100)/(',(daycount*4)-1,'))');
#select qq;
prepare stt2 from @qr;
EXECUTE stt2;
DEALLOCATE prepare stt2 ;


if page_count=0 and rows_count=0 THEN
set @limitq='';
else


set page_count=page_count-1;
set pagelimit=page_count*rows_count;
set @limitq=CONCAT('limit ',pagelimit,',',rows_count);
end if;
set @qrr=CONCAT('CREATE TEMPORARY TABLE IF NOT EXISTS finaltable AS (select ',@finalselect,' from a  order by deviation desc ',@limitq,' );');
prepare stt21 from @qrr;
EXECUTE stt21;
DEALLOCATE prepare stt21 ;

select * from finaltable;


#drop table inputpricestemp;
drop table if exists inputpricestemp;
drop table if exists a;
drop table if exists finaltable;
END$$


--
-- Functions
--
CREATE FUNCTION `fn_getoutput` (`inputprice` DOUBLE, `outputprice` DOUBLE) RETURNS DOUBLE BEGIN
set @returnval=0;
set inputprice=ABS(inputprice);
set outputprice=ABS(outputprice);
if inputprice=0 and outputprice=0 then
set @returnval=100;
elseif GREATEST(inputprice,outputprice) !=0 THEN
set @returnval=(100 - ((greatest(inputprice, outputprice) - least(inputprice, outputprice)) / greatest(inputprice, outputprice)) * 100);
ELSE
set @returnval=(100 - ((least(inputprice, outputprice) - greatest(inputprice, outputprice)) / least(inputprice, outputprice)) * 100);
end if;
return @returnval;
END$$

CREATE FUNCTION `fn_getpercent` (`otherprice` DOUBLE, `closeprice` DOUBLE) RETURNS DOUBLE BEGIN
DECLARE retval double;
#if closeprice=0 then
set retval=0;

RETURN retval;
END$$

DELIMITER ;

