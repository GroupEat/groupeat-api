<?php namespace Groupeat\Orders\Html;

use Auth;
use Config;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Forms\ConfirmForm;
use Groupeat\Support\Exceptions\Exception;
use Groupeat\Support\Html\Controller;
use Input;

class GroupOrdersController extends Controller {

    public function showConfirmForm(GroupOrder $groupOrder, $token)
    {
        $this->assertAuthSameFrom($groupOrder->restaurant, $token);

        if ($groupOrder->isConfirmed())
        {
            $preparedAt = formatTime($groupOrder->prepared_at);

            return $this->panelView(
                'orders::confirmation.success.title',
                trans('orders::confirmation.success.text', compact('preparedAt')),
                'success'
            );
        }

        try
        {
            $content = new ConfirmForm(
                $groupOrder,
                Config::get('orders::maximum_preparation_time_in_minutes')
            );
        }
        catch (Exception $exception)
        {
            $content = trans('orders::confirmation.'.$exception->getErrorKey());
        }

        return $this->panelView('orders::confirmation.form.title', $content, 'danger');
    }

    public function confirm(GroupOrder $groupOrder, $token)
    {
        $this->assertAuthSameFrom($groupOrder->restaurant, $token);
        $groupOrderConfirmationService = app('ConfirmGroupOrderService');

        try
        {
            $preparedAt = formatTime($groupOrderConfirmationService->call(
                $groupOrder,
                Input::get('preparedAt')
            ));

            return $this->panelView(
                'orders::confirmation.success.title',
                trans('orders::confirmation.success.text', compact('preparedAt')),
                'success'
            );
        }
        catch (Exception $exception)
        {
            return $this->redirectBackWithError('orders::confirmation.'.$exception->getErrorKey(), [
                'maximumMinutes' => $groupOrderConfirmationService->getMaximumPreparationTimeInMinutes(),
            ]);
        }
    }

}
