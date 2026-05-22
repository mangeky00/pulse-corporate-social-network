const AppConfig = window.App || {};

const csrfToken = AppConfig.csrfToken || "";
const currentUserId = Number(AppConfig.userId || 0);

const fetchJson = async (url, options = {}) => {
    const response = await fetch(url, {
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
            ...(options.headers || {}),
        },
        ...options,
    });

    if (!response.ok) {
        throw new Error(`Request failed with status ${response.status}`);
    }

    return response.json();
};

const escapeHtml = (value = "") =>
    value
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");

const renderAttachments = (attachments = []) =>
    attachments
        .map((attachment) => {
            if (attachment.file_type === "image") {
                return `<a href="${attachment.url}" target="_blank" rel="noopener"><img src="${attachment.url}" alt="${escapeHtml(attachment.file_name)}"></a>`;
            }

            return `<a href="${attachment.url}">${escapeHtml(attachment.file_name)}</a>`;
        })
        .join("");

const renderMessage = (message, type) => {
    const own = Number(message.sender_id) === currentUserId;
    const senderMeta = !own && type === "group"
        ? `<strong>${escapeHtml(message.sender_name)}</strong>`
        : "";
    const avatar = !own
        ? `<img src="${message.sender_avatar_url}" alt="${escapeHtml(message.sender_name)}">`
        : "";
    const body = message.body ? `<p>${escapeHtml(message.body).replaceAll("\n", "<br>")}</p>` : "";
    const attachments = message.attachments?.length
        ? `<div class="message-attachments">${renderAttachments(message.attachments)}</div>`
        : "";

    return `
        <article class="message-bubble ${own ? "message-bubble--own" : ""}">
            ${avatar}
            <div class="message-bubble__content">
                ${senderMeta}
                ${body}
                ${attachments}
                <span>${escapeHtml(message.created_at_label || "")}</span>
            </div>
        </article>
    `;
};

const renderConversationCard = (conversation, activeType, activeId) => {
    const active = conversation.type === activeType && Number(conversation.id) === Number(activeId);
    const badge = conversation.unread_count > 0
        ? `<span class="nav-badge">${conversation.unread_count}</span>`
        : "";

    return `
        <a href="${conversation.href}"
            class="conversation-card ${active ? "is-active" : ""}"
            data-conversation-card
            data-conversation-type="${conversation.type}"
            data-conversation-id="${conversation.id}">
            <img src="${conversation.avatar_url}" alt="${escapeHtml(conversation.name)}">
            <div class="conversation-card__body">
                <strong>${escapeHtml(conversation.name)}</strong>
                <span>${escapeHtml(conversation.subtitle || "")}</span>
            </div>
            ${badge}
        </a>
    `;
};

const updateUnreadBadge = (conversations) => {
    const badge = document.querySelector("[data-unread-badge]");
    if (!badge) {
        return;
    }

    const total = conversations.reduce((sum, item) => sum + Number(item.unread_count || 0), 0);

    if (total > 0) {
        badge.textContent = String(total);
        badge.classList.remove("is-hidden");
    } else {
        badge.textContent = "";
        badge.classList.add("is-hidden");
    }
};

const initCommentInteractions = () => {
    document.querySelectorAll("[data-comment-toggle]").forEach((button) => {
        button.addEventListener("click", () => {
            const shell = button.closest("[data-post-card]")?.querySelector("[data-comments-shell]");
            if (shell) {
                shell.classList.toggle("is-hidden");
            }
        });
    });

    document.querySelectorAll("[data-like-button]").forEach((button) => {
        button.addEventListener("click", async () => {
            try {
                const data = await fetchJson(button.dataset.likeUrl, { method: "POST" });
                button.classList.toggle("is-liked", Boolean(data.liked));
                const count = button.querySelector("[data-like-count]");
                if (count) {
                    count.textContent = String(data.likes_count);
                }
            } catch (error) {
                console.error(error);
            }
        });
    });

    document.querySelectorAll("[data-comment-form]").forEach((form) => {
        form.addEventListener("submit", async (event) => {
            event.preventDefault();

            const input = form.querySelector('input[name="body"]');
            if (!input || !input.value.trim()) {
                return;
            }

            try {
                const data = await fetchJson(form.dataset.commentUrl, {
                    method: "POST",
                    body: new URLSearchParams({ body: input.value.trim() }),
                });

                const list = form.parentElement?.querySelector(".comment-list");
                if (list && data.comment) {
                    const deleteButton = data.comment.can_delete
                        ? `<button type="button" class="comment-delete" data-delete-comment data-delete-url="/comments/${data.comment.id}">Удалить</button>`
                        : "";

                    list.insertAdjacentHTML("beforeend", `
                        <article class="comment-card" data-comment-id="${data.comment.id}">
                            <img src="${data.comment.user.avatar_url}" alt="${escapeHtml(data.comment.user.name)}">
                            <div>
                                <div class="comment-card__meta">
                                    <strong>${escapeHtml(data.comment.user.name)}</strong>
                                    <span>${escapeHtml(data.comment.created_at || "")}</span>
                                </div>
                                <p>${escapeHtml(data.comment.body)}</p>
                            </div>
                            ${deleteButton}
                        </article>
                    `);
                }

                const counter = form.closest("[data-post-card]")?.querySelector("[data-comment-count]");
                if (counter) {
                    counter.textContent = String(data.comments_count);
                }

                input.value = "";
                initDeleteCommentButtons();
            } catch (error) {
                console.error(error);
            }
        });
    });
};

const initDeleteCommentButtons = () => {
    document.querySelectorAll("[data-delete-comment]").forEach((button) => {
        if (button.dataset.bound === "true") {
            return;
        }

        button.dataset.bound = "true";
        button.addEventListener("click", async () => {
            if (!confirm("Удалить комментарий?")) {
                return;
            }

            try {
                const data = await fetchJson(button.dataset.deleteUrl, {
                    method: "DELETE",
                });

                const post = button.closest("[data-post-card]");
                button.closest(".comment-card")?.remove();

                const counter = post?.querySelector("[data-comment-count]");
                if (counter) {
                    counter.textContent = String(data.comments_count);
                }
            } catch (error) {
                console.error(error);
            }
        });
    });
};

const initModals = () => {
    document.querySelectorAll("[data-modal-open]").forEach((button) => {
        button.addEventListener("click", () => {
            document.querySelector(`[data-modal="${button.dataset.modalOpen}"]`)?.classList.add("is-open");
        });
    });

    document.querySelectorAll("[data-modal-close]").forEach((button) => {
        button.addEventListener("click", () => {
            button.closest("[data-modal]")?.classList.remove("is-open");
        });
    });

    document.querySelectorAll("[data-modal]").forEach((modal) => {
        modal.addEventListener("click", (event) => {
            if (event.target === modal) {
                modal.classList.remove("is-open");
            }
        });
    });
};

const applyTheme = (theme) => {
    document.documentElement.dataset.theme = theme;

    try {
        localStorage.setItem("pulse-theme", theme);
    } catch (error) {
        console.error(error);
    }

    const nextThemeLabel = theme === "light" ? "Включить темную тему" : "Включить светлую тему";

    document.querySelectorAll("[data-theme-toggle]").forEach((button) => {
        button.dataset.themeMode = theme;
        button.setAttribute("aria-label", nextThemeLabel);
        button.setAttribute("title", nextThemeLabel);
        button.setAttribute("aria-pressed", String(theme === "light"));
    });

    document.querySelectorAll("[data-theme-toggle-label]").forEach((node) => {
        node.textContent = nextThemeLabel;
    });
};

const initThemeToggle = () => {
    const buttons = document.querySelectorAll("[data-theme-toggle]");
    if (!buttons.length) {
        return;
    }

    const storedTheme = document.documentElement.dataset.theme === "light" ? "light" : "dark";
    applyTheme(storedTheme);

    buttons.forEach((button) => {
        button.addEventListener("click", () => {
            const nextTheme = document.documentElement.dataset.theme === "light" ? "dark" : "light";
            applyTheme(nextTheme);
        });
    });
};

const initUserSearch = () => {
    const input = document.querySelector("[data-user-search-input]");
    const items = Array.from(document.querySelectorAll("[data-user-search-item]"));
    const emptyState = document.querySelector("[data-user-search-empty]");

    if (!input || !items.length) {
        return;
    }

    const updateList = () => {
        const query = input.value.trim().toLowerCase();
        let visibleItems = 0;

        items.forEach((item) => {
            const text = (item.dataset.searchText || "").toLowerCase();
            const matches = query === "" || text.includes(query);
            item.classList.toggle("is-hidden", !matches);

            if (matches) {
                visibleItems += 1;
            }
        });

        if (emptyState) {
            emptyState.classList.toggle("is-hidden", visibleItems > 0);
        }
    };

    input.addEventListener("input", updateList);
    updateList();
};

const initMessages = () => {
    const page = document.querySelector("[data-messages-page]");
    if (!page || !currentUserId) {
        return;
    }

    const stream = document.querySelector("[data-messages-stream]");
    const form = document.querySelector("[data-message-form]");
    const directList = document.querySelector('[data-conversation-list="direct"]');
    const groupList = document.querySelector('[data-conversation-list="group"]');

    const activeType = stream?.dataset.type || null;
    const activeId = stream?.dataset.id || null;

    const refreshConversations = async () => {
        try {
            const conversations = await fetchJson(AppConfig.routes?.conversations || "/messages/conversations/data");
            updateUnreadBadge(conversations);

            if (directList) {
                const directItems = conversations.filter((item) => item.type === "direct");
                directList.innerHTML = directItems.length
                    ? directItems.map((item) => renderConversationCard(item, activeType, activeId)).join("")
                    : '<div class="empty-state empty-state--compact"><p>Личных диалогов пока нет.</p></div>';
            }

            if (groupList) {
                const groupItems = conversations.filter((item) => item.type === "group");
                groupList.innerHTML = groupItems.length
                    ? groupItems.map((item) => renderConversationCard(item, activeType, activeId)).join("")
                    : '<div class="empty-state empty-state--compact"><p>Групповых чатов пока нет.</p></div>';
            }
        } catch (error) {
            console.error(error);
        }
    };

    const refreshMessages = async () => {
        if (!stream?.dataset.refreshUrl) {
            return;
        }

        try {
            const data = await fetchJson(stream.dataset.refreshUrl);
            if (!Array.isArray(data.messages)) {
                return;
            }

            const nearBottom = stream.scrollHeight - stream.scrollTop - stream.clientHeight < 100;
            stream.innerHTML = data.messages.map((message) => renderMessage(message, stream.dataset.type)).join("");

            if (nearBottom) {
                stream.scrollTop = stream.scrollHeight;
            }
        } catch (error) {
            console.error(error);
        }
    };

    const appendMessage = (message) => {
        if (!stream) {
            return;
        }

        stream.insertAdjacentHTML("beforeend", renderMessage(message, stream.dataset.type));
        stream.scrollTop = stream.scrollHeight;
    };

    if (form) {
        form.addEventListener("submit", async (event) => {
            event.preventDefault();

            const bodyInput = form.querySelector('input[name="body"]');
            const fileInput = form.querySelector('input[type="file"]');
            const formData = new FormData(form);

            const hasBody = bodyInput && bodyInput.value.trim() !== "";
            const hasFiles = fileInput?.files?.length > 0;

            if (!hasBody && !hasFiles) {
                return;
            }

            try {
                const data = await fetchJson(form.dataset.submitUrl, {
                    method: "POST",
                    body: formData,
                });

                if (data.message) {
                    appendMessage(data.message);
                }

                form.reset();
                refreshConversations();
            } catch (error) {
                console.error(error);
            }
        });
    }

    const connectRealtime = () => {
        const reverb = AppConfig.reverb || {};
        if (!window.Pusher || !reverb.key || !currentUserId) {
            return;
        }

        window.Pusher.logToConsole = false;

        const pusher = new window.Pusher(reverb.key, {
            wsHost: reverb.host,
            wsPort: reverb.port,
            wssPort: reverb.port,
            forceTLS: reverb.scheme === "https",
            enabledTransports: ["ws", "wss"],
            disableStats: true,
            authEndpoint: "/broadcasting/auth",
            auth: {
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                },
            },
        });

        const channel = pusher.subscribe(`private-users.${currentUserId}`);
        channel.bind("chat.message", (payload) => {
            refreshConversations();

            if (!stream) {
                return;
            }

            const sameConversation = String(stream.dataset.id) === String(payload.conversation_id)
                && String(stream.dataset.type) === String(payload.type);

            if (sameConversation && payload.message) {
                appendMessage(payload.message);
            }
        });
    };

    refreshConversations();
    if (stream) {
        stream.scrollTop = stream.scrollHeight;
        setInterval(refreshMessages, 12000);
    }
    setInterval(refreshConversations, 15000);
    connectRealtime();
};

const initUnreadBadgePolling = () => {
    if (!currentUserId || document.querySelector("[data-messages-page]")) {
        return;
    }

    const refresh = async () => {
        try {
            const conversations = await fetchJson(AppConfig.routes?.conversations || "/messages/conversations/data");
            updateUnreadBadge(conversations);
        } catch (error) {
            console.error(error);
        }
    };

    refresh();
    setInterval(refresh, 15000);
};

document.addEventListener("DOMContentLoaded", () => {
    initCommentInteractions();
    initDeleteCommentButtons();
    initModals();
    initThemeToggle();
    initUserSearch();
    initUnreadBadgePolling();
    initMessages();
});
