-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: MySQL-8.2
-- Время создания: Май 07 2026 г., 10:37
-- Версия сервера: 8.2.0
-- Версия PHP: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `socnetwork`
--

-- --------------------------------------------------------

--
-- Структура таблицы `comments`
--

CREATE TABLE `comments` (
  `id` bigint UNSIGNED NOT NULL,
  `post_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `comments`
--

INSERT INTO `comments` (`id`, `post_id`, `user_id`, `body`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'Соберу вопросы от продуктовой команды и пришлю единым списком к среде.', '2026-04-23 14:05:00', '2026-04-23 14:05:00'),
(2, 1, 3, 'Со стороны разработки подготовим короткий статус по миграции.', '2026-04-23 15:10:00', '2026-04-23 15:10:00'),
(3, 2, 4, 'Чек-лист для новичков уже обновила и добавила блок про документы.', '2026-04-25 10:30:00', '2026-04-25 10:30:00'),
(4, 2, 6, 'Для продаж еще нужен актуальный линк на презентацию для онбординга.', '2026-04-25 12:00:00', '2026-04-25 12:00:00'),
(5, 3, 5, 'Распечатаю новые таблички для кухни к утру пятницы.', '2026-04-26 07:20:00', '2026-04-26 07:20:00'),
(6, 3, 2, 'Спасибо, добавлю это в еженедельный дайджест.', '2026-04-26 08:10:00', '2026-04-26 08:10:00');

-- --------------------------------------------------------

--
-- Структура таблицы `group_chats`
--

CREATE TABLE `group_chats` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `group_chats`
--

INSERT INTO `group_chats` (`id`, `name`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Запуск портала', 1, '2026-04-27 05:00:00', '2026-04-27 05:00:00'),
(2, 'HR и адаптация', 4, '2026-04-27 05:15:00', '2026-04-27 05:15:00');

-- --------------------------------------------------------

--
-- Структура таблицы `group_members`
--

CREATE TABLE `group_members` (
  `id` bigint UNSIGNED NOT NULL,
  `group_chat_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'member',
  `last_read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `group_members`
--

INSERT INTO `group_members` (`id`, `group_chat_id`, `user_id`, `role`, `last_read_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'owner', '2026-04-27 09:20:00', '2026-04-27 09:20:00', '2026-04-27 09:20:00'),
(2, 1, 2, 'member', '2026-05-07 04:31:17', '2026-04-27 08:00:00', '2026-05-07 04:31:17'),
(3, 1, 3, 'member', '2026-05-07 04:28:34', '2026-04-27 07:30:00', '2026-05-07 04:28:34'),
(4, 1, 5, 'member', '2026-04-27 07:50:00', '2026-04-27 07:50:00', '2026-04-27 07:50:00'),
(5, 2, 4, 'owner', '2026-04-27 09:10:00', '2026-04-27 09:10:00', '2026-04-27 09:10:00'),
(6, 2, 1, 'member', '2026-04-27 06:55:00', '2026-04-27 06:55:00', '2026-04-27 06:55:00'),
(7, 2, 6, 'member', '2026-04-27 08:25:00', '2026-04-27 08:25:00', '2026-04-27 08:25:00');

-- --------------------------------------------------------

--
-- Структура таблицы `group_messages`
--

CREATE TABLE `group_messages` (
  `id` bigint UNSIGNED NOT NULL,
  `group_chat_id` bigint UNSIGNED NOT NULL,
  `sender_id` bigint UNSIGNED NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `group_messages`
--

INSERT INTO `group_messages` (`id`, `group_chat_id`, `sender_id`, `body`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'Обновленный текст анонса лежит в общей папке.', '2026-04-27 07:15:00', '2026-04-27 07:15:00'),
(2, 1, 5, 'Дизайн финального баннера готов, можно отдавать в публикацию.', '2026-04-27 07:45:00', '2026-04-27 07:45:00'),
(3, 1, 3, 'По API блокеров нет, чек-лист закрыт.', '2026-04-27 08:10:00', '2026-04-27 08:10:00'),
(4, 2, 4, 'Новая версия положения об отпусках готова к согласованию.', '2026-04-27 06:55:00', '2026-04-27 06:55:00'),
(5, 2, 6, 'Команда продаж посмотрела документ, осталась одна правка по формулировке.', '2026-04-27 07:35:00', '2026-04-27 07:35:00');

-- --------------------------------------------------------

--
-- Структура таблицы `group_message_attachments`
--

CREATE TABLE `group_message_attachments` (
  `id` bigint UNSIGNED NOT NULL,
  `group_message_id` bigint UNSIGNED NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `likes`
--

CREATE TABLE `likes` (
  `id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` int NOT NULL,
  `post_id` int NOT NULL,
  `like` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `likes`
--

INSERT INTO `likes` (`id`, `created_at`, `updated_at`, `user_id`, `post_id`, `like`) VALUES
(1, '2026-04-27 11:46:26', '2026-04-27 11:46:26', 2, 1, 1),
(2, '2026-04-27 11:46:26', '2026-04-27 11:46:26', 3, 1, 1),
(3, '2026-04-27 11:46:26', '2026-04-27 11:46:26', 4, 1, 1),
(4, '2026-04-27 11:46:26', '2026-04-27 11:46:26', 5, 1, 1),
(6, '2026-04-27 11:46:26', '2026-04-27 11:46:26', 6, 2, 1),
(7, '2026-04-27 11:46:26', '2026-04-27 11:46:26', 5, 2, 1),
(8, '2026-04-27 11:46:26', '2026-04-27 11:46:26', 2, 3, 1),
(9, '2026-05-07 04:29:24', '2026-05-07 04:29:24', 3, 3, 1),
(10, '2026-05-07 04:29:26', '2026-05-07 04:29:26', 3, 2, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `messages`
--

CREATE TABLE `messages` (
  `id` bigint UNSIGNED NOT NULL,
  `sender_id` bigint UNSIGNED NOT NULL,
  `receiver_id` bigint UNSIGNED NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `body`, `read_at`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'Подготовь, пожалуйста, анонс запуска до 14:00.', '2026-04-27 11:47:06', '2026-04-27 05:30:00', '2026-04-27 11:47:06'),
(2, 2, 1, 'Черновик готов. Жду только финальную цифру от финансов.', '2026-04-27 06:45:00', '2026-04-27 06:10:00', '2026-04-27 06:10:00'),
(3, 2, 3, 'Посмотри, пожалуйста, роадмап на второй квартал до обеда.', '2026-05-07 04:28:39', '2026-04-27 07:25:00', '2026-05-07 04:28:39'),
(4, 3, 2, 'Да, через двадцать минут оставлю комментарии в документе.', '2026-04-27 11:47:04', '2026-04-27 07:55:00', '2026-04-27 11:47:04'),
(5, 4, 6, 'Подтверди, пожалуйста, численность по плану найма на май.', NULL, '2026-04-27 06:40:00', '2026-04-27 06:40:00'),
(6, 6, 4, 'Подтвердила. Отправлю подписанный лист сразу после звонка с клиентом.', '2026-04-27 07:20:00', '2026-04-27 07:05:00', '2026-04-27 07:05:00'),
(7, 1, 4, 'После общего созвона нужно синхронизироваться по обновлению регламента.', NULL, '2026-04-27 08:15:00', '2026-04-27 08:15:00'),
(8, 3, 2, '123', '2026-05-07 04:31:55', '2026-05-07 04:31:44', '2026-05-07 04:31:55'),
(9, 2, 3, '321', '2026-05-07 04:33:10', '2026-05-07 04:33:04', '2026-05-07 04:33:10');

-- --------------------------------------------------------

--
-- Структура таблицы `message_attachments`
--

CREATE TABLE `message_attachments` (
  `id` bigint UNSIGNED NOT NULL,
  `message_id` bigint UNSIGNED NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(13, '2024_05_23_144552_create_users_table', 1),
(14, '2024_05_24_113211_create_posts_table', 1),
(15, '2024_05_25_170224_create_likes_table', 1),
(16, '2026_04_26_190100_expand_users_table_for_corporate_network', 1),
(17, '2026_04_26_190200_create_comments_table', 1),
(18, '2026_04_26_190300_create_post_attachments_table', 1),
(19, '2026_04_26_190400_create_messages_table', 1),
(20, '2026_04_26_190500_create_message_attachments_table', 1),
(21, '2026_04_26_190600_create_group_chats_table', 1),
(22, '2026_04_26_190700_create_group_members_table', 1),
(23, '2026_04_26_190800_create_group_messages_table', 1),
(24, '2026_04_26_190900_create_group_message_attachments_table', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `posts`
--

CREATE TABLE `posts` (
  `id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `posts`
--

INSERT INTO `posts` (`id`, `created_at`, `updated_at`, `body`, `user_id`) VALUES
(1, '2026-04-23 13:00:00', '2026-04-23 13:00:00', 'В четверг в 16:00 проводим общий созвон в большом конференц-зале. Вопросы к встрече можно прислать заранее.', 1),
(2, '2026-04-25 07:30:00', '2026-04-25 07:30:00', 'Обновили чек-лист адаптации в базе знаний. Руководителям нужно пройти его с каждым новым сотрудником в первую неделю.', 1),
(3, '2026-04-26 06:15:00', '2026-04-26 06:15:00', 'В эту пятницу командный завтрак пройдет на кухне третьего этажа. Пожелания по меню собираем в офисной форме до вечера.', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `post_attachments`
--

CREATE TABLE `post_attachments` (
  `id` bigint UNSIGNED NOT NULL,
  `post_id` bigint UNSIGNED NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `position` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Employee',
  `department` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'General',
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'avatars/default-avatar.svg',
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `created_at`, `updated_at`, `first_name`, `email`, `password`, `remember_token`, `last_name`, `position`, `department`, `phone`, `avatar`, `role`, `last_login`) VALUES
(1, '2026-04-27 11:46:26', '2026-04-27 11:46:26', 'Александр', 'admin@socnetwork.local', '$2y$12$dMW8oGlkw8L7QHD3bCcZqO7aQCX7kYxee232C9DoSJZNqd/nys0GK', NULL, 'Картер', 'Директор по операциям', 'Администрация', '+7 (495) 100-00-00', 'avatars/default-avatar.svg', 'admin', '2026-04-27 09:45:00'),
(2, '2026-04-27 11:46:26', '2026-05-07 04:31:03', 'Оливия', 'olivia.brooks@socnetwork.local', '$2y$12$dMW8oGlkw8L7QHD3bCcZqO7aQCX7kYxee232C9DoSJZNqd/nys0GK', NULL, 'Брукс', 'Продуктовый менеджер', 'Продукт', '+7 (495) 100-00-01', 'avatars/default-avatar.svg', 'user', '2026-05-07 04:31:03'),
(3, '2026-04-27 11:46:26', '2026-05-07 04:28:14', 'Макс', 'max.turner@socnetwork.local', '$2y$12$dMW8oGlkw8L7QHD3bCcZqO7aQCX7kYxee232C9DoSJZNqd/nys0GK', NULL, 'Тернер', 'Backend-разработчик', 'Разработка', '+7 (495) 100-00-02', 'avatars/default-avatar.svg', 'user', '2026-05-07 04:28:14'),
(4, '2026-04-27 11:46:26', '2026-04-27 11:46:26', 'Нина', 'nina.park@socnetwork.local', '$2y$12$dMW8oGlkw8L7QHD3bCcZqO7aQCX7kYxee232C9DoSJZNqd/nys0GK', NULL, 'Парк', 'HR-менеджер', 'HR', '+7 (495) 100-00-03', 'avatars/default-avatar.svg', 'user', '2026-04-27 07:40:00'),
(5, '2026-04-27 11:46:26', '2026-04-27 11:46:26', 'Лео', 'leo.sanders@socnetwork.local', '$2y$12$dMW8oGlkw8L7QHD3bCcZqO7aQCX7kYxee232C9DoSJZNqd/nys0GK', NULL, 'Сандерс', 'Продуктовый дизайнер', 'Дизайн', '+7 (495) 100-00-04', 'avatars/default-avatar.svg', 'user', '2026-04-27 07:10:00'),
(6, '2026-04-27 11:46:26', '2026-04-27 11:46:26', 'Эмма', 'emma.hughes@socnetwork.local', '$2y$12$dMW8oGlkw8L7QHD3bCcZqO7aQCX7kYxee232C9DoSJZNqd/nys0GK', NULL, 'Хьюз', 'Руководитель продаж', 'Продажи', '+7 (495) 100-00-05', 'avatars/default-avatar.svg', 'user', '2026-04-27 06:35:00');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comments_post_id_foreign` (`post_id`),
  ADD KEY `comments_user_id_foreign` (`user_id`);

--
-- Индексы таблицы `group_chats`
--
ALTER TABLE `group_chats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_chats_created_by_foreign` (`created_by`);

--
-- Индексы таблицы `group_members`
--
ALTER TABLE `group_members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `group_members_group_chat_id_user_id_unique` (`group_chat_id`,`user_id`),
  ADD KEY `group_members_user_id_foreign` (`user_id`);

--
-- Индексы таблицы `group_messages`
--
ALTER TABLE `group_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_messages_group_chat_id_foreign` (`group_chat_id`),
  ADD KEY `group_messages_sender_id_foreign` (`sender_id`);

--
-- Индексы таблицы `group_message_attachments`
--
ALTER TABLE `group_message_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_message_attachments_group_message_id_foreign` (`group_message_id`);

--
-- Индексы таблицы `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `messages_sender_id_foreign` (`sender_id`),
  ADD KEY `messages_receiver_id_foreign` (`receiver_id`);

--
-- Индексы таблицы `message_attachments`
--
ALTER TABLE `message_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `message_attachments_message_id_foreign` (`message_id`);

--
-- Индексы таблицы `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `post_attachments`
--
ALTER TABLE `post_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_attachments_post_id_foreign` (`post_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `comments`
--
ALTER TABLE `comments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `group_chats`
--
ALTER TABLE `group_chats`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `group_members`
--
ALTER TABLE `group_members`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `group_messages`
--
ALTER TABLE `group_messages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `group_message_attachments`
--
ALTER TABLE `group_message_attachments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `likes`
--
ALTER TABLE `likes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `message_attachments`
--
ALTER TABLE `message_attachments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT для таблицы `posts`
--
ALTER TABLE `posts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `post_attachments`
--
ALTER TABLE `post_attachments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `group_chats`
--
ALTER TABLE `group_chats`
  ADD CONSTRAINT `group_chats_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `group_members`
--
ALTER TABLE `group_members`
  ADD CONSTRAINT `group_members_group_chat_id_foreign` FOREIGN KEY (`group_chat_id`) REFERENCES `group_chats` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `group_members_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `group_messages`
--
ALTER TABLE `group_messages`
  ADD CONSTRAINT `group_messages_group_chat_id_foreign` FOREIGN KEY (`group_chat_id`) REFERENCES `group_chats` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `group_messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `group_message_attachments`
--
ALTER TABLE `group_message_attachments`
  ADD CONSTRAINT `group_message_attachments_group_message_id_foreign` FOREIGN KEY (`group_message_id`) REFERENCES `group_messages` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_receiver_id_foreign` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `message_attachments`
--
ALTER TABLE `message_attachments`
  ADD CONSTRAINT `message_attachments_message_id_foreign` FOREIGN KEY (`message_id`) REFERENCES `messages` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `post_attachments`
--
ALTER TABLE `post_attachments`
  ADD CONSTRAINT `post_attachments_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
