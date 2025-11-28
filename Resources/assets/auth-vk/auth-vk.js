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
 *
 */
executeFunc(function authVk()
{
    const authLink = document.getElementById('vk-auth');

    console.log(authLink)

    if(typeof authLink === "undefined" || authLink === null)
    {
        return false;
    }

    authLink.addEventListener('click', function(event)
    {
        /** Закрываем модальное окно */
        const modal = document.getElementById("modal");

        if(modal)
        {
            let currentmodal = bootstrap.Modal.getOrCreateInstance(modal);
            currentmodal.hide();
        }

        /* отменяем обычный переход */
        event.preventDefault();

        const authUrl = this.href;

        const width = 600;
        const height = 700;
        const left = (screen.width - width) / 2;
        const top = (screen.height - height) / 2;

        const features = `width=${width},height=${height},left=${left},top=${top},resizable=yes,scrollbars=yes`

        const authWindow = window.open(authUrl, 'vkAuth', features);

        if(authWindow)
        {
            authWindow.focus();
        } else
        {
            alert('Не удалось открыть окно авторизации. Разрешите всплывающие окна.');
        }
    });

    window.addEventListener('message', function(event)
    {
        /* Сообщение от js на странице /auth/vk */
        if(event.data.type === 'VK_AUTH_SUCCESS')
        {
            /* Редирект на главную */
            window.location.replace(window.location.origin);
        }
    });

    return true;
});