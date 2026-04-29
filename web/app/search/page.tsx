import type { Metadata } from "next";
import Link from "next/link";
import { search } from "@/lib/api";
import { Container } from "@/components/Container";

export const metadata: Metadata = {
  title: "Search",
  description:
    "Search across practices, services, advisors, and briefs.",
};

type SearchParams = { q?: string };

export default async function SearchPage({
  searchParams,
}: {
  searchParams: Promise<SearchParams>;
}) {
  const { q = "" } = await searchParams;
  const trimmed = q.trim();
  const data = trimmed ? await search(trimmed) : null;

  return (
    <Container className="py-20">
      <div className="max-w-2xl">
        <p className="text-xs uppercase tracking-[0.2em] text-muted">
          Search
        </p>
        <h1 className="mt-3 text-4xl font-semibold tracking-tight sm:text-5xl">
          {trimmed ? `Results for "${trimmed}"` : "What are you looking for?"}
        </h1>
      </div>

      <form
        action="/search"
        method="GET"
        className="mt-8 flex max-w-xl gap-3"
      >
        <input
          type="search"
          name="q"
          defaultValue={trimmed}
          placeholder="Try “RSU” or “tax loss harvesting”"
          className="flex-1 rounded-full border border-border bg-surface px-5 py-3 text-base outline-none focus:border-text"
        />
        <button
          type="submit"
          className="rounded-full bg-primary px-6 py-3 text-sm font-medium text-white hover:bg-primary-strong transition-colors"
        >
          Search
        </button>
      </form>

      {!trimmed ? (
        <p className="mt-12 text-muted">
          Search across practices, services, advisors, and briefs.
        </p>
      ) : data ? (
        <Results data={data} />
      ) : null}
    </Container>
  );
}

function Results({
  data,
}: {
  data: NonNullable<Awaited<ReturnType<typeof search>>>;
}) {
  const { results } = data;
  const total =
    results.divisions.length +
    results.services.length +
    results.advisors.length +
    results.articles.length;

  if (total === 0) {
    return (
      <p className="mt-12 text-muted">
        No matches found. Try different keywords.
      </p>
    );
  }

  return (
    <div className="mt-12 space-y-12">
      {results.divisions.length > 0 ? (
        <Group title="Practices">
          <ul className="space-y-2">
            {results.divisions.map((d) => (
              <li key={d.id}>
                <Link
                  href={`/divisions/${d.slug}`}
                  className="block rounded-lg border border-border bg-surface p-4 hover:shadow-md transition-shadow"
                >
                  <p className="font-medium">{d.title}</p>
                  <p className="text-sm text-muted line-clamp-1">
                    {d.short_description}
                  </p>
                </Link>
              </li>
            ))}
          </ul>
        </Group>
      ) : null}

      {results.services.length > 0 ? (
        <Group title="Services">
          <ul className="space-y-2">
            {results.services.map((s) => (
              <li key={s.id}>
                <Link
                  href={`/services/${s.slug}`}
                  className="block rounded-lg border border-border bg-surface p-4 hover:shadow-md transition-shadow"
                >
                  <p className="font-medium">{s.title}</p>
                  <p className="text-sm text-muted line-clamp-1">
                    {s.summary}
                  </p>
                </Link>
              </li>
            ))}
          </ul>
        </Group>
      ) : null}

      {results.advisors.length > 0 ? (
        <Group title="Advisors">
          <ul className="space-y-2">
            {results.advisors.map((a) => (
              <li key={a.id}>
                <Link
                  href={`/advisors/${a.slug}`}
                  className="block rounded-lg border border-border bg-surface p-4 hover:shadow-md transition-shadow"
                >
                  <p className="font-medium">{a.name}</p>
                  <p className="text-sm text-muted">{a.role}</p>
                </Link>
              </li>
            ))}
          </ul>
        </Group>
      ) : null}

      {results.articles.length > 0 ? (
        <Group title="Briefs">
          <ul className="space-y-2">
            {results.articles.map((a) => (
              <li key={a.id}>
                <Link
                  href={`/insights/${a.slug}`}
                  className="block rounded-lg border border-border bg-surface p-4 hover:shadow-md transition-shadow"
                >
                  <p className="font-medium">{a.title}</p>
                  <p className="text-sm text-muted line-clamp-1">
                    {a.excerpt}
                  </p>
                </Link>
              </li>
            ))}
          </ul>
        </Group>
      ) : null}
    </div>
  );
}

function Group({
  title,
  children,
}: {
  title: string;
  children: React.ReactNode;
}) {
  return (
    <div>
      <p className="text-xs uppercase tracking-[0.2em] text-muted">
        {title}
      </p>
      <div className="mt-4">{children}</div>
    </div>
  );
}
