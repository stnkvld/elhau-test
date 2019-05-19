<?php

namespace app\commands;

use yii\console\Controller;
use app\models\Task;
use app\models\Result;
use GuzzleHttp\Client;
use yii\helpers\Console;

class TaskController extends Controller
{
    public function actionPerformAll() {
        $tasks = Task::find()->select('id')->where(['<>', 'status', 1])->all();
        $headers = [
            'Cookie' => 'f=5.cc913c231fb04ced4b5abdd419952845a68643d4d8df96e9a68643d4d8df96e9a68643d4d8df96e9a68643d4d8df96e94f9572e6986d0c624f9572e6986d0c624f9572e6986d0c62ba029cd346349f36c1e8912fd5a48d02c1e8912fd5a48d0246b8ae4e81acb9fa143114829cf33ca746b8ae4e81acb9fa46b8ae4e81acb9fae992ad2cc54b8aa8af305aadb1df8cebc93bf74210ee38d940e3fb81381f3591fed88e598638463b2da10fb74cac1eab2da10fb74cac1eab2da10fb74cac1eab2da10fb74cac1eab2da10fb74cac1eab2da10fb74cac1eab2da10fb74cac1eab2da10fb74cac1eab2da10fb74cac1eab2da10fb74cac1eab2da10fb74cac1eab2da10fb74cac1eabfdb229eec3b9ed9a0c79affd4e5f1d11162fe9fd7c8e9767bc91a9c0405bfff0b9a105bb47e903365e61d702b2ac73f71b4d31ba3578021fbae804e0bf5c02c0f067aba0e25496fe5c0f8f4cbaf68ed671e7cb57bbcb8e0f8f1786dad6fd98129e82118971f2ed64956cdff3d4067aa52d5497dd6e774d368117dbeb4b629ffb3de19da9ed218fe23de19da9ed218fe2356d8209912c0256f17cf55851d2137489c0e3d5ba80b84c',
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.157 Safari/537.36'
        ];

        if (!empty($tasks)) {
            $this->stdout("Идет парсинг объявлений\n", Console::FG_YELLOW);

            foreach ($tasks as $task) {
                $task->status = 2;
                $task->update(false);
                $this->actionPerform($task->id, $headers);
            }

            $this->stdout("Парсинг завершен\n", Console::FG_GREEN);
        } else
            $this->stdout("Задания в очереди отсутствуют\n", Console::FG_RED);
    }

    public function actionPerform($id, $headers) {
        $task = Task::findOne($id);

        if (!empty($task)) {
            $client = new Client();

            $res = $client->request('GET', $task->url, [
                'headers' => $headers
            ]);

            $body = $res->getBody();
            $document = \phpQuery::newDocumentHTML($body);

            if (array_search('/blocked', $res->getHeaders()['x-request-url']) === false) {

                $lastPageItem = $document->find('.pagination-page')->filter(':last')->attr('href');

                if (!empty($lastPageItem)) {
                    $lastPageIndex = parse_url($lastPageItem);
                    preg_match('/.*p=(\d+)/', $lastPageIndex['query'], $lastPageIndex);
                    $lastPageIndex = (int) $lastPageIndex[1];
                } else
                    $lastPageIndex = 1;

                for ($i=1; $i<=$lastPageIndex; ++$i) {
                    $adLinksHtml = $document->find(".item-description-title-link");

                    foreach ($adLinksHtml as $adLinkHtml) {
                        $adLinkHtml = pq($adLinkHtml);
                        $adLink = 'https://www.avito.ru' . $adLinkHtml->attr('href');

                        if (empty(Result::findOne(['avito_url' => $adLink]))) {

                            sleep(rand(2, 5));

                            $ad = $client->request('GET', $adLink, [
                                'headers' => $headers
                            ]);

                            $adBody = $ad->getBody();
                            $adDocument = \phpQuery::newDocumentHTML($adBody);

                            $params = [];
                            foreach ($adDocument->find('.item-params-list-item') as $paramItem) {
                                $paramItem = pq($paramItem);
                                $params[] = trim($paramItem->html());
                            }

                            $metro = [];
                            foreach ($adDocument->find('.item-map-metro') as $metroItem) {
                                $metroItem = pq($metroItem);
                                $metroItem->find('.i-metro')->remove();
                                $metro[] = trim($metroItem->html());
                            }

                            $images = [];
                            foreach ($adDocument->find('.gallery-img-wrapper') as $imageItem) {
                                $imageItem = pq($imageItem);
                                $images[] = $imageItem->find('.gallery-img-frame')->attr('data-url');
                            }

                            $ads = [
                                'title' => trim($adDocument->find('.title-info-title-text')->html()),
                                'price' => trim($adDocument->find('.js-item-price[itemprop="price"]')->attr('content')),
                                'address' => trim($adDocument->find('.item-map-address span[itemprop="streetAddress"]')->html()),
                                'params' => $params,
                                'metro' => $metro,
                                'images' => $images,
                                'description' => trim($adDocument->find('.item-description-text')->html())
                            ];

                            $result = new Result();

                            $result->task_id = $id;
                            $result->avito_url = $adLink;
                            $result->data = json_encode($ads);

                            if ($result->validate() && $result->save()) {
                                continue;
                            }

                            $this->stdout("Ошибка парсинга объявления $adLink\n", Console::FG_RED);
                            exit;
                        }
                    }

                    $query = parse_url($task->url, PHP_URL_QUERY);
                    $changedTaskUrl = $task->url . ($query ? "&p=$i" : "?p=$i");

                    $res = $client->request('GET', $changedTaskUrl, [
                        'headers' => $headers
                    ]);

                    $body = $res->getBody();
                    $document = \phpQuery::newDocumentHTML($body);
                }

                $task->status = 1;
                $task->update(false);

            } else
                $this->stdout("Avito заблокировал IP-адрес\n", Console::FG_RED);
        }
    }
}
