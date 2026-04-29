/**
 * Renders a <script type="application/ld+json"> with the given data.
 * Server Component — safe with `dangerouslySetInnerHTML` because the input
 * is JSON-serialized by us, not user-supplied text.
 */
export function JsonLd({ data }: { data: object }) {
  return (
    <script
      type="application/ld+json"
      dangerouslySetInnerHTML={{ __html: JSON.stringify(data) }}
    />
  );
}
