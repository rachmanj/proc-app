/* SELECT FROM OPOR T0 */
DECLARE @Date AS DATETIME
DECLARE @EndDate AS DATETIME
SET @Date = /* T0.CreateDate */ '[%0]'
SET @EndDate = /* T0.Createdate */ '[%1]'

Select distinct
A.DocNum[po_no],
A.DocDate[posting_date],
A.CreateDate[create_date],
--D.DocDate as [grpo_date],
A.U_MIS_DeliveryTime [po_delivery_date],
A.U_MIS_EstArrival [po_eta],
H.DocNum [pr_no],
A.CardCode as [vendor_code],
A.CardName as [vendor_name],
B.U_MIS_UnitNo as [unit_no],
B.ItemCode [item_code],
B.Dscription [description],
B.U_MIS_ConsRe1 [remark1],
B.U_MIS_ConsRe2 [remark2],
B.Quantity as [qty],
B.Currency [po_currency],
B.Price as [unit_price],
B.Quantity*B.Price as [item_amount],
A.DocTotalFC-A.VatSumFC+A.DiscSumFC as [total_po_price],
A.DocTotalFC [po_with_vat],
B.unitMsr as [uom],
B.Project as [project_code],
cc.Code as [dept_code],
CASE A.DocStatus 
WHEN 'O' Then 'Open' 
When 'C' Then CASE A.Canceled WHEN 'Y' THEN 'Cancelled' ELSE 'Closed' END
Else A.DocStatus End 
as [po_status],
CASE A.U_ARK_DelivStat WHEN 'Y' THEN 'Delivered' WHEN 'N' THEN 'Not Delivered' END [po_delivery_status],
A.U_ARK_BudgetType as [budget_type]
--D.DocNum as [grpo_no]
	FROM OPOR A
	INNER JOIN POR1 B ON A.DocEntry = B.DocEntry
LEFT JOIN [@MIS_CCDPT] cc on A.U_MIS_CCDepartement = cc.Code
	--LEFT JOIN PDN1 C ON B.DocEntry = C.BaseEntry and B.LineNum = C.BaseLine 
	--LEFT JOIN OPDN D ON D.DocEntry = C.DocEntry 
	--LEFT JOIN RPD1 E ON c.DocEntry=E.BaseEntry AND c.ItemCode=E.ItemCode AND c.LineNum=E.BaseLine 
	--LEFT JOIN ORPD F ON E.DocEntry = F.DocEntry 
              --LEFT JOIN OSLP G ON A.SlpCode = G.SlpCode
              LEFT JOIN OPRQ H ON A.U_MIS_PRNo = H.DocNum
	--LEFT JOIN PCH1 I ON D.DocEntry = I.BaseEntry AND C.LineNum = I.BaseLine AND C.ItemCode = I.ItemCode
	--LEFT JOIN OPCH J ON I.DocEntry = J.DocEntry
              --LEFT JOIN VPM2 K ON J.transid = K.doctransid
              --LEFT JOIN OVPM L ON K.DocNum = L.DocNum
              --LEFT JOIN RPC1 M ON M.BaseEntry = J.DocEntry AND M.BaseLine = I.LineNum
              --LEFT JOIN ORPC N ON M.DocEntry = N.DocEntry
              --LEFT JOIN DPO1 O ON O.BaseEntry = B.DocEntry AND O.BaseLine = B.LineNum
              --LEFT JOIN ODPO P ON O.DocEntry = P.DocEntry
	Where A.CreateDate >= @Date And A.DocDate <= @EndDate
FOR BROWSE