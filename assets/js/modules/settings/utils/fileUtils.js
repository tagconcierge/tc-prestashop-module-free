export function downloadObjectAsJson(exportObj, exportName) {
  const dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(exportObj, null, 2));
  const downloadAnchorNode = document.createElement('a');
  downloadAnchorNode.setAttribute("href", dataStr);
  downloadAnchorNode.setAttribute("download", exportName + ".json");
  document.body.appendChild(downloadAnchorNode);
  downloadAnchorNode.click();
  downloadAnchorNode.remove();
}

export function isPresetLocked(preset, isPro) {
  if (isPro) return false;

  if (Object.prototype.hasOwnProperty.call(preset, 'locked')) {
    return Boolean(preset.locked);
  }

  return false;
} 