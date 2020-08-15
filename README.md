## Информация
[https://paybox.money/kz_ru/dev](https://paybox.money/kz_ru/dev)

## Примеры использования
```
// Cоздание тестового счета на оплату
$paybox = new \Sakhnovkrg\Paybox\PayboxClient(
    123456, 'secret', 'random', true);

$orderId = 1;
$orderCost = 500;

$invoice = $paybox->createInvoice($orderId, $orderCost, 'Описание заказа')
    ->setUserEmail('test@test.test')
    ->setUserPhone('77776665544')
    ->setParam1("Адрес доставки");

$data = $invoice->getData();

$redirectUrl = $data->getRedirectUrl();
$paymentId = $data->getPaymentId();

header('Location:' . $redirectUrl);
```
```
// Проверка статуса платежа
$paymentId = 999999999;
$paymentStatus = $paybox->getPaymentStatus($paymentId);
$transactionStatus = $paymentStatus->getTransactionStatus();
```
