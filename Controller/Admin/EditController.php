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
use BaksDev\Auth\Vk\UseCase\Admin\NewEdit\AccountVkEditDTO;
use BaksDev\Auth\Vk\UseCase\Admin\NewEdit\AccountVkEditForm;
use BaksDev\Auth\Vk\UseCase\Admin\NewEdit\AccountVkEditHandler;
use BaksDev\Core\Controller\AbstractController;
use BaksDev\Core\Listeners\Event\Security\RoleSecurity;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[RoleSecurity('ROLE_ACCOUNT_VK_EDIT')]
final class EditController extends AbstractController
{

    #[Route('/admin/account/vk/edit/{id}', name: 'admin.newedit.edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        #[MapEntity] AccountVkEvent $AccountVkEvent,
        AccountVkEditHandler $AccountVkHandler,
    ): Response
    {

        $AccountVkDTO = new AccountVkEditDTO();

        $AccountVkEvent->getDto($AccountVkDTO);

        /* Форма  */
        $form = $this
            ->createForm(AccountVkEditForm::class, $AccountVkDTO, [
                'action' => $this->generateUrl(
                    'auth-vk:admin.newedit.edit',
                    ['id' => $AccountVkDTO->getEvent()]
                ),
            ])
            ->handleRequest($request);


        if($form->isSubmitted() && $form->isValid() && $form->has('account_vk'))
        {

            $this->refreshTokenForm($form);

            $handle = $AccountVkHandler->handle($AccountVkDTO);

            $this->addFlash
            (
                'page.edit',
                $handle instanceof AccountVk ? 'success.edit' : 'danger.edit',
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
