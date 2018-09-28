DELIMITER $$
--
-- Procedures
--

DROP PROCEDURE IF EXISTS `sp_detailed_fifteenth_analysis_closePrice`$$
CREATE PROCEDURE `sp_detailed_fifteenth_analysis_closePrice` (IN `commodity` VARCHAR(100), IN `daycount` INT, IN `inputprices` TEXT, IN `diff` DOUBLE, IN `page_count` INT, IN `rows_count` INT)  BEGIN

declare pagelimit int default 1; # initialize the page limit which will change as per user define
declare i int UNSIGNED default 0; # initializing the Incremental value for creating select and where clause string from user defined table
declare ii int UNSIGNED default 0; # initializing the Incremental value of input price storing into a temporary table
declare iii int UNSIGNED default 0; # initializing the Incremental value for creating a conditional statement after fetching the data from user defined table. This conditional statement will use for calculating the fetced data and give the actual deviation
declare qq text DEFAULT ''; # Initialize a temporary query string
declare minlimit DOUBLE DEFAULT 0; # initialize minimum limit of fetching the data from table
declare maxlimit DOUBLE DEFAULT 0; # initialize maximum limit of fetching the data from table
declare inputopenprice DOUBLE DEFAULT 0; 
declare inputhighprice DOUBLE DEFAULT 0;
declare inputlowprice DOUBLE DEFAULT 0;
declare inputcloseprice DOUBLE DEFAULT 0;
declare inputfinalcloseprice DOUBLE DEFAULT 0;# initialize a variable for storing last day close price vlaue 
set @table_name=''; # initialize a variable for storing user defined table name
set @selectlist=''; # This is for creating a select statement string for fetching the raw data from table
set @selectlist1=''; # This is for creating a select statement string for calculating open,close,highest and low price from the raw data
set @selectlist2=''; # This is for creating a select statement string for calculating input deviation of open,close,highest and low price from the raw data
set @selectlist3=''; # This is for creating a select statement string for calculating output deviation of open,close,highest and low price from the raw data
set @wherelist1=''; # This is for creating a where statement string for ID
set @wherelist2=''; # This is for creating a where statement string for Sell Year
set @wherelist3=''; # This is for creating a where statement string for Sell Month
set @wherelist4=''; # This is for creating a where statement string for required combination like s1.open_price-s4.close_price.....
create TEMPORARY TABLE if not EXISTS inputpricestemp #create a temporary table for storing input price
(
id int UNSIGNED DEFAULT 0,
open_price decimal(8,2) DEFAULT 0,
highest_price decimal(8,2) DEFAULT 0,
lowest_price decimal(8,2) DEFAULT 0,
closed_price decimal(8,2) DEFAULT 0
);
set ii=0;
WHILE ii<daycount DO # insert input price into temporary table "inputpricestemp"
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


WHILE i<daycount DO # create all the input price related select and where statement
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

# start extra row count from here. This is for getting the last day closeup, closedown and close ecual calculation


set @wherelist1=CONCAT(@wherelist1,'s',i,'.','id+1=s',i+1,'.','id and ');
set i=daycount+1;
set @table_name=CONCAT(@table_name,commodity,' s',i,',');
set @table_name=SUBSTRING(@table_name,1,CHAR_LENGTH(@table_name)-1);

set @selectlist=CONCAT(@selectlist,'s',i,'.','id id_',i,',s',i,'.','open_price open_price_',i,',s',i,'.','highest_price highest_price_'
,i,',s',i,'.','lowest_price lowest_price_',i,',s',i,'.','closed_price closed_price_',i,',s',i,'.','stockdate stock_date_',i,',s',i,'.','sellyear sellyear_',i,
',s',i,'.','sellmonth sellmonth_',i,',');


# end extra row functionality

set @selectlist2=CONCAT(@selectlist2,'0000.00 as outputdeviation,'); # initialization for outputdeviation
set @selectlist2=CONCAT(@selectlist2,'0000.00 as close_diff,'); # initialization for last day open_price-close_price difference
set @selectlist2=CONCAT(@selectlist2,'0000.00 as close_up,'); # initialization for how many close_up found.
set @selectlist2=CONCAT(@selectlist2,'0000.00 as close_down,'); # initialization for how many close_down found.
set @selectlist2=CONCAT(@selectlist2,'0000.00 as close_equal'); # initialization for how many close_equal found.
set @wherelist4=SUBSTRING(@wherelist4,1,CHAR_LENGTH(@wherelist4)-3);
set @wherelist=CONCAT(@wherelist1,@wherelist2,@wherelist3,@wherelist4);

if @wherelist!='' THEN
set @wherelist=CONCAT(' where ',@wherelist);
end if;
#create a temporary table for fetching all the fetched data into a temporary table which name is "a"
set @query=CONCAT('CREATE TEMPORARY TABLE IF NOT EXISTS a as (SELECT ',@selectlist,@selectlist1,@selectlist2,' FROM ',@table_name,' ',@wherelist,')');
PREPARE stt1 from @query;
EXECUTE stt1;
DEALLOCATE prepare stt1;

# copy "a" table to another table like "b". this is for updating "a" table some file like how many close_up,down and equal.
set @query=CONCAT('CREATE TEMPORARY TABLE IF NOT EXISTS c as (SELECT ',@selectlist,@selectlist1,@selectlist2,' FROM ',@table_name,' ',@wherelist,')');
PREPARE stt1 from @query;
EXECUTE stt1;
DEALLOCATE prepare stt1;
set iii=0;
set @update='';
set @finalselect='';
WHILE iii<daycount DO # create sql statement in fetched data for getting the output deviation
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
end WHILE;

set qq=SUBSTRING(qq,1,CHAR_LENGTH(qq)-1);

set @qr=CONCAT('update a set close_diff=(closed_price_',daycount+1,'-open_price_',daycount+1,')'); # update close_diif based on last day open_price-close_price in table "a"

prepare stt2 from @qr;
EXECUTE stt2;
DEALLOCATE prepare stt2 ;

set @qr=CONCAT('update c set close_diff=(closed_price_',daycount+1,'-open_price_',daycount+1,')'); # update close_diif based on last day open_price-close_price in table "b"

prepare stt2 from @qr;
EXECUTE stt2;
DEALLOCATE prepare stt2 ;

set @qr=CONCAT('update a set close_equal = (select count(*) from c where close_diff=0)'); # update close_equal in table "a" by claculating how many rows difference are equal
prepare stt21 from @qr;
EXECUTE stt21;
DEALLOCATE prepare stt21 ;
set @qr=CONCAT('update a set close_down = (select count(*) from c where close_diff<0)'); # update close_down in table "a" by claculating how many rows difference are down
prepare stt21 from @qr;
EXECUTE stt21;
DEALLOCATE prepare stt21 ;
set @qr=CONCAT('update a set close_up = (select count(*) from c where close_diff>0)'); # update close_up in table "a" by claculating how many rows difference are up
prepare stt21 from @qr;
EXECUTE stt21;
DEALLOCATE prepare stt21 ;

set @finalselect=CONCAT(@finalselect,'outputdeviation deviation,'); # concate all the select option with deviation aswell
set @finalselect=CONCAT(@finalselect,'close_diff,');
set @finalselect=CONCAT(@finalselect,'close_up,');
set @finalselect=CONCAT(@finalselect,'close_down,');
set @finalselect=CONCAT(@finalselect,'close_equal');
set @qr=CONCAT('update a set outputdeviation=(((',qq,')-100)/(',(daycount*4)-1,'))'); # update output deviation by deducting the last close_price-close_price = 100, because there is rules that if value is same then add 100, So finaly removed the close_last-close_last.

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
set @qrr=CONCAT('CREATE TEMPORARY TABLE IF NOT EXISTS finaltable AS (select ',@finalselect,' from a order by deviation desc ',@limitq,' );'); # create a temporary table with all the required filed need to return
prepare stt21 from @qrr;
EXECUTE stt21;
DEALLOCATE prepare stt21 ;

select * from finaltable; # Finally select full temporary table for returning 

drop table if exists inputpricestemp; # delete temporary table
drop table if exists a;
drop table if exists c;
drop table if exists finaltable;

END$$


DELIMITER ;
