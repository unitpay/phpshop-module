<p>ѕлатежи через сервис <b>Unitpay</b> Ц это хороший выбор.<p>
<form method="GET" name="pay" id="pay" action="@pay_url@">
<input type="hidden" name="account" value="@account@">
<input type="hidden" name="sum" value="@sum@">
<input type="hidden" name="signature" value="@signature@">
<input type="hidden" name="currency" value="@currency@">
<input type="hidden" name="customerEmail" value="@customerEmail@">
<input type="hidden" name="customerPhone" value="@customerPhone@">
<input type="hidden" name="desc" value="@desc@">
<input type="hidden" name="cashItems" value="@cartItems@">

<button type="submit">ќплатить через платежную систему</button>
</form>