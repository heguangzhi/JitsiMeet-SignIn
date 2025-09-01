// 管理界面JavaScript功能
// Admin Interface JavaScript Functions

document.addEventListener('DOMContentLoaded', function() {
    // Generate invitation code form handling
    // 生成邀请码表单处理
    const generateForm = document.getElementById('generateForm');
    if (generateForm) {
        generateForm.addEventListener('submit', function(e) {
            e.preventDefault();
            generateCode();
        });
    }
});

// 生成新邀请码
// Generate new invitation code
function generateCode() {
    const formData = new FormData(document.getElementById('generateForm'));
    formData.append('action', 'generate');
    
    const button = document.querySelector('#generateForm button');
    const originalText = button.textContent;
    button.textContent = '生成中...';
    button.disabled = true;
    
    fetch('admin.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('邀请码生成成功：' + data.code, 'success');
            // 重新加载页面以更新列表
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            showMessage('生成失败，请重试', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('网络错误，请重试', 'error');
    })
    .finally(() => {
        button.textContent = originalText;
        button.disabled = false;
    });
}

// 切换邀请码状态
// Toggle invitation code status
function toggleCode(id) {
    if (!confirm('确定要切换此邀请码的状态吗？')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'toggle');
    formData.append('id', id);
    
    fetch('admin.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('状态更新成功', 'success');
            // 重新加载页面以更新列表
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showMessage('操作失败，请重试', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('网络错误，请重试', 'error');
    });
}

// 删除邀请码
// Delete invitation code
function deleteCode(id) {
    if (!confirm('确定要删除此邀请码吗？此操作不可撤销！')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', id);
    
    fetch('admin.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('删除成功', 'success');
            // 重新加载页面以更新列表
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showMessage('删除失败，请重试', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('网络错误，请重试', 'error');
    });
}

// 显示消息提示
// Show message notification
function showMessage(message, type = 'info') {
    // 移除已存在的消息
    const existingMessage = document.querySelector('.message-toast');
    if (existingMessage) {
        existingMessage.remove();
    }
    
    // 创建新消息元素
    const messageDiv = document.createElement('div');
    messageDiv.className = `message-toast ${type}`;
    messageDiv.textContent = message;
    
    // 添加样式
    messageDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 6px;
        color: white;
        font-weight: 500;
        z-index: 1000;
        opacity: 0;
        transform: translateX(100px);
        transition: all 0.3s ease;
    `;
    
    // 根据类型设置背景颜色
    switch (type) {
        case 'success':
            messageDiv.style.backgroundColor = '#28a745';
            break;
        case 'error':
            messageDiv.style.backgroundColor = '#dc3545';
            break;
        default:
            messageDiv.style.backgroundColor = '#17a2b8';
    }
    
    // 添加到页面
    document.body.appendChild(messageDiv);
    
    // 显示动画
    setTimeout(() => {
        messageDiv.style.opacity = '1';
        messageDiv.style.transform = 'translateX(0)';
    }, 100);
    
    // 自动移除
    setTimeout(() => {
        messageDiv.style.opacity = '0';
        messageDiv.style.transform = 'translateX(100px)';
        setTimeout(() => {
            if (messageDiv.parentNode) {
                messageDiv.parentNode.removeChild(messageDiv);
            }
        }, 300);
    }, 3000);
}

// 复制邀请码到剪贴板
// Copy invitation code to clipboard
function copyCode(code) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(code).then(() => {
            showMessage('邀请码已复制到剪贴板', 'success');
        }).catch(() => {
            fallbackCopyTextToClipboard(code);
        });
    } else {
        fallbackCopyTextToClipboard(code);
    }
}

// 备用复制方法
// Fallback copy method
function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.top = "0";
    textArea.style.left = "0";
    textArea.style.position = "fixed";
    
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        document.execCommand('copy');
        showMessage('邀请码已复制到剪贴板', 'success');
    } catch (err) {
        showMessage('复制失败，请手动复制', 'error');
    }
    
    document.body.removeChild(textArea);
}

// 为邀请码单元格添加点击复制功能
document.addEventListener('DOMContentLoaded', function() {
    const codeCells = document.querySelectorAll('.code-cell');
    codeCells.forEach(cell => {
        cell.style.cursor = 'pointer';
        cell.title = '点击复制邀请码';
        cell.addEventListener('click', function() {
            copyCode(this.textContent);
        });
    });
});