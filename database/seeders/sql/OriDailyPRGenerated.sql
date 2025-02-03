Declare @A AS DATETIME
Declare @B AS DATETIME
/*WHERE*/
SET @A= /* a.CreateDate */ '[%0]'
SET @B= /* a.CreateDate */ '[%1]'
Select distinct
-- I.U_MIS_WoNo [WO No],
-- J.U_MIS_WODate [WO Date],
-- CASE J.Status 
-- 		WHEN -3 THEN 'Open' 
-- 		WHEN -2 THEN 'Pending' 
-- 		WHEN -1 THEN 'Closed'
-- 		WHEN 1 THEN 'Canceled'
-- 		WHEN 2 THEN 'Surcharge'
-- 	END AS [WO Status],
-- A.U_MIS_MRNO [MR No]
-- ,M.DocNum [MI No]
-- ,M.DocDate [MI Date]
-- ,G1.DocNum [PO No]
-- ,G1.DocDate [PO Date]
-- ,G1.U_MIS_EstArrival [ETA] 
-- ,case when G1.DocStatus ='O' THEN 'Open' when G1.DocStatus='C' then 'Closed' End [PO Status]
-- ,CASE G1.U_ARK_DelivStat WHEN 'Y' THEN 'Delivered' WHEN 'N' THEN 'Not Delivered' END [PO Delivery Status]
-- ,G1.U_MIS_DeliveryTime [PO Delivery Time]
-- ,(SELECT TOP 1 A1.DocNum GrpoNO from OPDN A1 LEFT JOIN PDN1 B1 ON A1.DocEntry=B1.DocEntry WHERE B1.BASEREF=G1.DocNum ORDER BY A1.DocEntry,A1.DocDate DESC)  [Last GRPO No]
-- ,(SELECT TOP 1 A1.DocDate GrpoDate from OPDN A1 LEFT JOIN PDN1 B1 ON A1.DocEntry=B1.DocEntry WHERE B1.BASEREF=G1.DocNum ORDER BY A1.DocEntry,A1.DocDate DESC) [Last GRPO Date]
A.draftKey [PR Draft No.]
,A.DocNum [PR No.]
,A.DocDate [PR Date]
,A.CreateDate [Generated Date]
,CASE A.U_MIS_Priority2 
	WHEN 'P1' THEN 'Breakdown'
	WHEN 'P2' THEN 'Backlog'
	WHEN 'P3' THEN 'Stock'
END [Priority]
,case when A.DocStatus='O' THEN 'Open' when A.DocStatus='C' AND A.CANCELED='N' then 'Closed' when A.DocStatus='C' AND A.CANCELED='Y' then 'Canceled' End  [PR Status],
a.U_MIS_ExpStatus 'Closed Status'
,a.U_MIS_PRRevNo [PR Rev No]
,case when A.DocType='I' THEN 'Item' when A.DocStatus='S' then 'Service' End as [PR Type]
,N.Name [Project Code]
,D.Remarks [Dept. Name]
,B.U_MIS_UnitNo [For Unit]
,B.U_MIS_HoursMeter [Hours-meter]
,B.PQTReqDate [Required Date]
,F.U_NAME [Requestor]
-- ,CASE g1.slpcode WHEN '1' THEN 'Ramon' WHEN '2' THEN 'Bambang' WHEN '3' THEN 'Johni' WHEN '5' THEN 'Embang' WHEN '8' THEN 'Bobby' WHEN '9' THEN 'Cucu' WHEN '10' THEN 'Tania' WHEN '11' THEN 'Christin' END as [Buyer]
,B.ItemCode [Item/Service Code]
,b.Dscription [Item/Service Name]
,B.Quantity 
-- ,B.U_MIS_Instock[Instock Per Whs]
,b.unitMsr [Uom]
,b.OpenQty [Open Qty]
,b.U_MIS_LineRemarks [Line Remarks]
-- ,'' Spesification
-- ,A.U_MIS_IssuePurpose Purpose
-- ,F1.Name [Symtomps Of Failure]
-- ,F2.Name [Status Category]
-- ,F3.Name [Status Alat]
-- ,b.LineVendor [Suggested Vendor]
-- ,G1.CardCode
-- ,G1.CardName
-- ,'' [Ship To]
-- ,'' [Transit To]
-- ,H.TrnspName [Shipment Method]
,a.Comments [Remarks]
from OPRQ A 
INNER JOIN PRQ1 B ON A.DocEntry=B.DocEntry
LEFT JOIN OUDP D ON A.Department=D.Code
LEFT JOIN OUSR F ON A.Requester=F.USER_CODE
LEFT JOIN OCRD E ON B.LineVendor=E.CardCode
LEFT JOIN [@MIS_SYMTOMPSFLR] F1 ON B.U_MIS_SymFailure=F1.Code
LEFT JOIN [@MIS_STATUSCAT] F2 ON B.U_MIS_StCategory=F2.Code
LEFT JOIN [@MIS_STATUSALAT] F3 ON B.U_MIS_StAlat=F3.Code
LEFT JOIN POR1 G ON B.DocEntry=G.BaseEntry AND B.ItemCode=G.ItemCode AND B.LineNum=G.BaseLine  
LEFT JOIN OPOR G1 ON G.DocEntry=G1.DocEntry
LEFT JOIN OSHP H ON B.TrnsCode=H.TrnspCode
LEFT JOIN ORDR I ON A.U_MIS_MRNO = I.DocNum
LEFT JOIN OSCL J ON I.U_MIS_WoNo = J.DocNum
LEFT JOIN ORDR K ON A.U_MIS_MRNO = K.DocNum
LEFT JOIN RDR1 L ON K.DocEntry = L.DocEntry
LEFT JOIN ODLN M ON L.TrgetEntry = M.DocEntry
LEFT JOIN OUBR N ON A.Branch = N.Code
WHERE A.[CreateDate] >= @A AND A.[CreateDate]  <= @B