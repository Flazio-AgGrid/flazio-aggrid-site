import { ElNotification } from "element-plus";

export function showNotification(
  title: string,
  message: string,
  type: "success" | "warning" | "info" | "error" = "info"
): void {
  ElNotification({
    title: title,
    message: message,
    type: type,
    position: "bottom-right",
  });
}
