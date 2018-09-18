CREATE PROCEDURE `sp_detailed_fifteenth_analysis_closePrice` (IN `commodity` VARCHAR(100), IN `daycount` INT, IN `inputprices` TEXT, IN `diff` DOUBLE, IN `page_count` INT, IN `rows_count` INT)  BEGIN
declare pagelimit int default 1;
declare daycount2 int default 1;
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
set daycount2 = daycount+1;
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

# start extra row count from here 


set @wherelist1=CONCAT(@wherelist1,'s',i,'.','id+1=s',i+1,'.','id and ');
set i=daycount+1;
set @table_name=CONCAT(@table_name,commodity,' s',i,',');
set @table_name=SUBSTRING(@table_name,1,CHAR_LENGTH(@table_name)-1);
#select @table_name;
set @selectlist=CONCAT(@selectlist,'s',i,'.','id id_',i,',s',i,'.','open_price open_price_',i,',s',i,'.','highest_price highest_price_'
,i,',s',i,'.','lowest_price lowest_price_',i,',s',i,'.','closed_price closed_price_',i,',s',i,'.','stockdate stock_date_',i,',s',i,'.','sellyear sellyear_',i,
',s',i,'.','sellmonth sellmonth_',i,',');


# end extra row functionality



#set @selectlist=SUBSTRING(@selectlist,1,CHAR_LENGTH(@selectlist)-1);
#set @selectlist2=SUBSTRING(@selectlist2,1,CHAR_LENGTH(@selectlist2)-1);
set @selectlist2=CONCAT(@selectlist2,'0000.00 as outputdeviation,');
set @selectlist2=CONCAT(@selectlist2,'0000.00 as close_diff,');
set @selectlist2=CONCAT(@selectlist2,'0000.00 as close_up,');
set @selectlist2=CONCAT(@selectlist2,'0000.00 as close_down,');
set @selectlist2=CONCAT(@selectlist2,'0000.00 as close_equal');
#set @wherelist3=SUBSTRING(@wherelist3,1,CHAR_LENGTH(@wherelist3)-4);
set @wherelist4=SUBSTRING(@wherelist4,1,CHAR_LENGTH(@wherelist4)-3);
set @wherelist=CONCAT(@wherelist1,@wherelist2,@wherelist3,@wherelist4);
#select qq;

if @wherelist!='' THEN
set @wherelist=CONCAT(' where ',@wherelist);
end if;
#set @wherelist='';

set @query=CONCAT('CREATE TEMPORARY TABLE IF NOT EXISTS a as (SELECT ',@selectlist,@selectlist1,@selectlist2,' FROM ',@table_name,' ',@wherelist,');');
PREPARE stt1 from @query;
EXECUTE stt1;
DEALLOCATE prepare stt1;


set @query=CONCAT('CREATE TEMPORARY TABLE IF NOT EXISTS b as (SELECT * FROM a)');
PREPARE stt1 from @query;
EXECUTE stt1;
DEALLOCATE prepare stt1;


#set @totalfoundrows = (select count(*) as totalrow from a);
#select @totalfoundrows;
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


#set @res=0;
set qq=SUBSTRING(qq,1,CHAR_LENGTH(qq)-1);
#set @aa = qq;
#set qq=@update;
#select qq;

set @qr=CONCAT('update a set close_diff=(open_price_',daycount+1,'-closed_price_',daycount+1,')');
#select qq;
prepare stt2 from @qr;
EXECUTE stt2;
DEALLOCATE prepare stt2 ;

set @qr=CONCAT('update b set close_diff=(open_price_',daycount+1,'-closed_price_',daycount+1,')');
#select qq;
prepare stt2 from @qr;
EXECUTE stt2;
DEALLOCATE prepare stt2 ;




set @qr=CONCAT('update a set close_equal = (select count(*) from b where close_diff=0)');
prepare stt21 from @qr;
EXECUTE stt21;
DEALLOCATE prepare stt21 ;
set @qr=CONCAT('update a set close_down = (select count(*) from b where close_diff<0)');
prepare stt21 from @qr;
EXECUTE stt21;
DEALLOCATE prepare stt21 ;
set @qr=CONCAT('update a set close_up = (select count(*) from b where close_diff>0)');
prepare stt21 from @qr;
EXECUTE stt21;
DEALLOCATE prepare stt21 ;

set @finalselect=CONCAT(@finalselect,'outputdeviation deviation,');
set @finalselect=CONCAT(@finalselect,'close_diff,');
set @finalselect=CONCAT(@finalselect,'close_up,');
set @finalselect=CONCAT(@finalselect,'close_down,');
set @finalselect=CONCAT(@finalselect,'close_equal');
set @qr=CONCAT('update a set outputdeviation=(((',qq,')-100)/(',(daycount*4)-1,'))');
#select qq;
prepare stt2 from @qr;
EXECUTE stt2;
DEALLOCATE prepare stt2 ;

#select * from a;



if page_count=0 and rows_count=0 THEN
set @limitq='';
else


set page_count=page_count-1;
set pagelimit=page_count*rows_count;
set @limitq=CONCAT('limit ',pagelimit,',',rows_count);
end if;
set @qrr=CONCAT('CREATE TEMPORARY TABLE IF NOT EXISTS finaltable AS (select ',@finalselect,' from a order by deviation desc ',@limitq,' );');
prepare stt21 from @qrr;
EXECUTE stt21;
DEALLOCATE prepare stt21 ;

select * from finaltable;





#drop table inputpricestemp;
drop table if exists inputpricestemp;
drop table if exists a;
drop table if exists finaltable;

END$$