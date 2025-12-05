<?php
/*
 *  Copyright 2025.  Baks.dev <admin@baks.dev>
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

declare(strict_types=1);

namespace BaksDev\Auth\Vk\Controller\Admin;


use BaksDev\Auth\Vk\Entity\AccountVk;
use BaksDev\Auth\Vk\Entity\Event\AccountVkEvent;
use BaksDev\Auth\Vk\UseCase\Admin\Delete\AccountVkDeleteDTO;
use BaksDev\Auth\Vk\UseCase\Admin\Delete\AccountVkDeleteForm;
use BaksDev\Auth\Vk\UseCase\Admin\Delete\AccountVkDeleteHandler;
use BaksDev\Core\Controller\AbstractController;
use BaksDev\Core\Listeners\Event\Security\RoleSecurity;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[RoleSecurity('ROLE_ACCOUNT_VK_DELETE')]
final class DeleteController extends AbstractController
{

    #[Route('/admin/account/vk/delete/{id}', name: 'admin.delete', methods: ['GET', 'POST'])]
    public function delete(
        Request $request,
        #[MapEntity] AccountVkEvent $AccountVkEvent,
        AccountVkDeleteHandler $AccountVkDeleteHandler,
    ): Response
    {

        $AccountVkDeleteDTO = new AccountVkDeleteDTO();
        $AccountVkEvent->getDto($AccountVkDeleteDTO);

        $form = $this->createForm(AccountVkDeleteForm::class, $AccountVkDeleteDTO, [
            'action' => $this->generateUrl(
                'auth-vk:admin.delete',
                ['id' => $AccountVkDeleteDTO->getEvent()]
            ),
        ]);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid() && $form->has('account_vk_delete'))
        {
            $this->refreshTokenForm($form);

            $handle = $AccountVkDeleteHandler->handle($AccountVkDeleteDTO);

            $this->addFlash
            (
                'page.delete',
                $handle instanceof AccountVk ? 'success.delete' : 'danger.delete',
                'auth-vk.admin',
                $handle
            );

            return $this->redirectToRoute('auth-vk:admin.index');
        }


        return $this->render([
            'form' => $form->createView(),
            'name' => $AccountVkEvent->getInvariable()->getVkid()
        ]);
    }
}
