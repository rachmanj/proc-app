Declare @A AS DATETIME
Declare @B AS DATETIME
/*WHERE*/
SET @A= /* a.CreateDate */ '[%0]'
SET @B= /* a.CreateDate */ '[%1]'
Select distinct
A.draftKey [pr_draft_no]
,A.DocNum [pr_no]
,A.DocDate [pr_date]
,A.CreateDate [generated_date]
,CASE A.U_MIS_Priority2 
	WHEN 'P1' THEN 'Breakdown'
	WHEN 'P2' THEN 'Backlog'
	WHEN 'P3' THEN 'Stock'
END [priority]
,case when A.DocStatus='O' THEN 'Open' when A.DocStatus='C' AND A.CANCELED='N' then 'Closed' when A.DocStatus='C' AND A.CANCELED='Y' then 'Canceled' End  [pr_status],
a.U_MIS_ExpStatus [closed_status]
,a.U_MIS_PRRevNo [pr_rev_no]
,case when A.DocType='I' THEN 'Item' when A.DocStatus='S' then 'Service' End as [pr_type]
,N.Name [project_code]
,D.Remarks [dept_name]
,B.U_MIS_UnitNo [for_unit]
,B.U_MIS_HoursMeter [hours_meter]
,B.PQTReqDate [required_date]
,F.U_NAME [requestor]
,B.ItemCode [item_service_code]
,b.Dscription [item_service_name]
,B.Quantity 
-- ,B.U_MIS_Instock[Instock Per Whs]
,b.unitMsr [uom]
,b.OpenQty [open_qty]
,b.U_MIS_LineRemarks [line_remarks]
,a.Comments [remarks]
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