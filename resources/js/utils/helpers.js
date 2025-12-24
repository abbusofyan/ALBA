export function resolveStatusVariant(status) {
  if (status === true) return "success";
  if (status === false) return "error";
  if (status === "Active") return "success";
  if (status === "Available") return "success";
  if (status === "Used") return "error";
  if (status === "New") return "info";
  if (status === "Redeemed") return "warning";
  if (status === "Failed") return "error";
  if (status === "Sent") return "success";
  if (status === "Banned") return "error";
  return "warning";
}
